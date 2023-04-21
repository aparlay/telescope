<?php

namespace Aparlay\Core\Admin\Models;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use OwenIt\Auditing\AuditableObserver;
use OwenIt\Auditing\Contracts\AttributeEncoder;
use OwenIt\Auditing\Contracts\AttributeRedactor;
use OwenIt\Auditing\Contracts\Resolver;
use OwenIt\Auditing\Events\AuditCustom;
use OwenIt\Auditing\Exceptions\AuditableTransitionException;
use OwenIt\Auditing\Exceptions\AuditingException;

trait Auditable
{
    /**
     * Auditable attributes excluded from the Audit.
     *
     * @var array
     */
    protected $excludedAttributes   = [];

    /**
     * Audit event name.
     *
     * @var string
     */
    public $auditEvent;

    /**
     * Is auditing disabled?
     *
     * @var bool
     */
    public static $auditingDisabled = false;

    /**
     * Property may set custom event data to register.
     *
     * @var array|null
     */
    public $auditCustomOld;

    /**
     * Property may set custom event data to register.
     *
     * @var array|null
     */
    public $auditCustomNew;

    /**
     * If this is a custom event (as opposed to an eloquent event.
     *
     * @var bool
     */
    public $isCustomEvent           = false;

    /**
     * Auditable boot logic.
     *
     * @return void
     */
    public static function bootAuditable()
    {
        if (!self::$auditingDisabled && static::isAuditingEnabled()) {
            static::observe(new AuditableObserver());
        }
    }

    public function audits(): MorphMany
    {
        return $this->morphMany(
            Config::get('audit.implementation', \OwenIt\Auditing\Models\Audit::class),
            'auditable'
        );
    }

    /**
     * Resolve the Auditable attributes to exclude from the Audit.
     *
     * @return void
     */
    protected function resolveAuditExclusions()
    {
        $this->excludedAttributes = $this->getAuditExclude();

        // When in strict mode, hidden and non visible attributes are excluded
        if ($this->getAuditStrict()) {
            // Hidden attributes
            $this->excludedAttributes = array_merge($this->excludedAttributes, $this->hidden);

            // Non visible attributes
            if ($this->visible) {
                $invisible                = array_diff(array_keys($this->attributes), $this->visible);

                $this->excludedAttributes = array_merge($this->excludedAttributes, $invisible);
            }
        }

        // Exclude Timestamps
        if (!$this->getAuditTimestamps()) {
            array_push($this->excludedAttributes, $this->getCreatedAtColumn(), $this->getUpdatedAtColumn());

            if (in_array(SoftDeletes::class, class_uses_recursive(get_class($this)))) {
                $this->excludedAttributes[] = $this->getDeletedAtColumn();
            }
        }
    }

    public function getAuditExclude(): array
    {
        return $this->auditExclude ?? Config::get('audit.exclude', []);
    }

    public function getAuditInclude(): array
    {
        return $this->auditInclude ?? [];
    }

    /**
     * Get the old/new attributes of a retrieved event.
     */
    protected function getRetrievedEventAttributes(): array
    {
        // This is a read event with no attribute changes,
        // only metadata will be stored in the Audit

        return [
            [],
            [],
        ];
    }

    /**
     * Get the old/new attributes of a created event.
     */
    protected function getCreatedEventAttributes(): array
    {
        $new = [];

        foreach ($this->attributes as $attribute => $value) {
            if ($this->isAttributeAuditable($attribute)) {
                $new[$attribute] = $value;
            }
        }

        return [
            [],
            $new,
        ];
    }

    protected function getCustomEventAttributes(): array
    {
        return [
            $this->auditCustomOld,
            $this->auditCustomNew,
        ];
    }

    /**
     * Get the old/new attributes of an updated event.
     */
    protected function getUpdatedEventAttributes(): array
    {
        $old = [];
        $new = [];

        foreach ($this->getDirty() as $attribute => $value) {
            if ($this->isAttributeAuditable($attribute)) {
                $old[$attribute] = Arr::get($this->original, $attribute);
                $new[$attribute] = Arr::get($this->attributes, $attribute);
            }
        }

        return [
            $old,
            $new,
        ];
    }

    /**
     * Get the old/new attributes of a deleted event.
     */
    protected function getDeletedEventAttributes(): array
    {
        $old = [];

        foreach ($this->attributes as $attribute => $value) {
            if ($this->isAttributeAuditable($attribute)) {
                $old[$attribute] = $value;
            }
        }

        return [
            $old,
            [],
        ];
    }

    /**
     * Get the old/new attributes of a restored event.
     */
    protected function getRestoredEventAttributes(): array
    {
        // A restored event is just a deleted event in reverse
        return array_reverse($this->getDeletedEventAttributes());
    }

    public function readyForAuditing(): bool
    {
        if (static::$auditingDisabled) {
            return false;
        }

        if ($this->isCustomEvent) {
            return true;
        }

        return $this->isEventAuditable($this->auditEvent);
    }

    /**
     * Modify attribute value.
     *
     * @param mixed $value
     *
     * @throws AuditingException
     *
     * @return mixed
     */
    protected function modifyAttributeValue(string $attribute, $value)
    {
        $attributeModifiers = $this->getAttributeModifiers();

        if (!array_key_exists($attribute, $attributeModifiers)) {
            return $value;
        }

        $attributeModifier  = $attributeModifiers[$attribute];

        if (is_subclass_of($attributeModifier, AttributeRedactor::class)) {
            return call_user_func([$attributeModifier, 'redact'], $value);
        }

        if (is_subclass_of($attributeModifier, AttributeEncoder::class)) {
            return call_user_func([$attributeModifier, 'encode'], $value);
        }

        throw new AuditingException(sprintf('Invalid AttributeModifier implementation: %s', $attributeModifier));
    }

    public function toAudit(): array
    {
        if (!$this->readyForAuditing()) {
            throw new AuditingException('A valid audit event has not been set');
        }

        $attributeGetter = $this->resolveAttributeGetter($this->auditEvent);

        if (!method_exists($this, $attributeGetter)) {
            throw new AuditingException(sprintf(
                'Unable to handle "%s" event, %s() method missing',
                $this->auditEvent,
                $attributeGetter
            ));
        }

        $this->resolveAuditExclusions();

        [$old, $new]     = $this->$attributeGetter();

        if ($this->getAttributeModifiers() && !$this->isCustomEvent) {
            foreach ($old as $attribute => $value) {
                $old[$attribute] = $this->modifyAttributeValue($attribute, $value);
            }

            foreach ($new as $attribute => $value) {
                $new[$attribute] = $this->modifyAttributeValue($attribute, $value);
            }
        }

        $morphPrefix     = Config::get('audit.user.morph_prefix', 'user');

        $tags            = implode(',', $this->generateTags());

        $user            = $this->resolveUser();

        return $this->transformAudit(array_merge([
            'old_values' => $old,
            'new_values' => $new,
            'event' => $this->auditEvent,
            'auditable_id' => $this->getKey(),
            'auditable_type' => $this->getMorphClass(),
            $morphPrefix . '_id' => $user ? $user->getAuthIdentifier() : null,
            $morphPrefix . '_type' => $user ? $user->getMorphClass() : null,
            'tags' => empty($tags) ? null : $tags,
        ], $this->runResolvers()));
    }

    public function transformAudit(array $data): array
    {
        return $data;
    }

    /**
     * Resolve the User.
     *
     * @throws AuditingException
     *
     * @return mixed|null
     */
    protected function resolveUser()
    {
        $userResolver = Config::get('audit.user.resolver');

        if (is_null($userResolver) && Config::has('audit.resolver') && !Config::has('audit.user.resolver')) {
            trigger_error(
                'The config file audit.php is not updated to the new version 13.0. Please see https://www.laravel-auditing.com/docs/13.0/upgrading',
                E_USER_DEPRECATED
            );
            $userResolver = Config::get('audit.resolver.user');
        }

        if (is_subclass_of($userResolver, \OwenIt\Auditing\Contracts\UserResolver::class)) {
            return call_user_func([$userResolver, 'resolve']);
        }

        throw new AuditingException('Invalid UserResolver implementation');
    }

    protected function runResolvers(): array
    {
        $resolved = [];
        if (Config::has('audit.resolver')) {
            trigger_error(
                'The config file audit.php is not updated to the new version 13.0. Please see https://www.laravel-auditing.com/docs/13.0/upgrading',
                E_USER_DEPRECATED
            );

            return [];
        }

        foreach (Config::get('audit.resolvers', []) as $name => $implementation) {
            if (empty($implementation)) {
                continue;
            }

            if (!is_subclass_of($implementation, Resolver::class)) {
                throw new AuditingException('Invalid Resolver implementation for: ' . $name);
            }
            $resolved[$name] = call_user_func([$implementation, 'resolve'], $this);
        }

        return $resolved;
    }

    /**
     * Determine if an attribute is eligible for auditing.
     */
    protected function isAttributeAuditable(string $attribute): bool
    {
        // The attribute should not be audited
        if (in_array($attribute, $this->excludedAttributes, true)) {
            return false;
        }

        // The attribute is auditable when explicitly
        // listed or when the include array is empty
        $include = $this->getAuditInclude();

        return empty($include) || in_array($attribute, $include, true);
    }

    /**
     * Determine whether an event is auditable.
     *
     * @param string $event
     */
    protected function isEventAuditable($event): bool
    {
        return is_string($this->resolveAttributeGetter($event));
    }

    /**
     * Attribute getter method resolver.
     *
     * @param string $event
     *
     * @return string|null
     */
    protected function resolveAttributeGetter($event)
    {
        if (empty($event)) {
            return;
        }

        if ($this->isCustomEvent) {
            return 'getCustomEventAttributes';
        }

        foreach ($this->getAuditEvents() as $key => $value) {
            $auditableEvent      = is_int($key) ? $value : $key;

            $auditableEventRegex = sprintf('/%s/', preg_replace('/\*+/', '.*', $auditableEvent));

            if (preg_match($auditableEventRegex, $event)) {
                return is_int($key) ? sprintf('get%sEventAttributes', ucfirst($event)) : $value;
            }
        }
    }

    public function setAuditEvent(string $event): \OwenIt\Auditing\Contracts\Auditable
    {
        $this->auditEvent = $this->isEventAuditable($event) ? $event : null;

        return $this;
    }

    public function getAuditEvent()
    {
        return $this->auditEvent;
    }

    public function getAuditEvents(): array
    {
        return $this->auditEvents ?? Config::get('audit.events', [
            'created',
            'updated',
            'deleted',
            'restored',
        ]);
    }

    /**
     * Disable Auditing.
     *
     * @return void
     */
    public static function disableAuditing()
    {
        static::$auditingDisabled = true;
    }

    /**
     * Enable Auditing.
     *
     * @return void
     */
    public static function enableAuditing()
    {
        static::$auditingDisabled = false;
    }

    /**
     * Determine whether auditing is enabled.
     */
    public static function isAuditingEnabled(): bool
    {
        if (App::runningInConsole()) {
            return Config::get('audit.enabled', true) && Config::get('audit.console', false);
        }

        return Config::get('audit.enabled', true);
    }

    public function getAuditStrict(): bool
    {
        return $this->auditStrict ?? Config::get('audit.strict', false);
    }

    public function getAuditTimestamps(): bool
    {
        return $this->auditTimestamps ?? Config::get('audit.timestamps', false);
    }

    public function getAuditDriver()
    {
        return $this->auditDriver ?? Config::get('audit.driver', 'database');
    }

    public function getAuditThreshold(): int
    {
        return $this->auditThreshold ?? Config::get('audit.threshold', 0);
    }

    public function getAttributeModifiers(): array
    {
        return $this->attributeModifiers ?? [];
    }

    public function generateTags(): array
    {
        return [];
    }

    public function transitionTo(\OwenIt\Auditing\Contracts\Audit $audit, bool $old = false): \OwenIt\Auditing\Contracts\Auditable
    {
        // The Audit must be for an Auditable model of this type
        if ($this->getMorphClass() !== $audit->auditable_type) {
            throw new AuditableTransitionException(sprintf(
                'Expected Auditable type %s, got %s instead',
                $this->getMorphClass(),
                $audit->auditable_type
            ));
        }

        // The Audit must be for this specific Auditable model
        if ($this->getKey() !== $audit->auditable_id) {
            throw new AuditableTransitionException(sprintf(
                'Expected Auditable id %s, got %s instead',
                $this->getKey(),
                $audit->auditable_id
            ));
        }

        // Redacted data should not be used when transitioning states
        foreach ($this->getAttributeModifiers() as $attribute => $modifier) {
            if (is_subclass_of($modifier, AttributeRedactor::class)) {
                throw new AuditableTransitionException('Cannot transition states when an AttributeRedactor is set');
            }
        }

        // The attribute compatibility between the Audit and the Auditable model must be met
        $modified = $audit->getModified();

        if ($incompatibilities = array_diff_key($modified, $this->getAttributes())) {
            throw new AuditableTransitionException(sprintf(
                'Incompatibility between [%s:%s] and [%s:%s]',
                $this->getMorphClass(),
                $this->getKey(),
                get_class($audit),
                $audit->getKey()
            ), array_keys($incompatibilities));
        }

        $key      = $old ? 'old' : 'new';

        foreach ($modified as $attribute => $value) {
            if (array_key_exists($key, $value)) {
                $this->setAttribute($attribute, $value[$key]);
            }
        }

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Pivot help methods
    |--------------------------------------------------------------------------
    |
    | Methods for auditing pivot actions
    |
    */

    /**
     * @param mixed $id
     * @param bool  $touch
     * @param mixed $columns
     *
     * @throws AuditingException
     *
     * @return void
     */
    public function auditAttach(string $relationName, $id, array $attributes = [], $touch = true, $columns = ['name'])
    {
        if (!method_exists($this, $relationName) || !method_exists($this->{$relationName}(), 'attach')) {
            throw new AuditingException('Relationship ' . $relationName . ' was not found or does not support method attach');
        }
        $this->auditEvent     = 'attach';
        $this->isCustomEvent  = true;
        $this->auditCustomOld = [
            $relationName => $this->{$relationName}()->get()->isEmpty() ? [] : $this->{$relationName}()->get()->toArray(),
        ];
        $this->{$relationName}()->attach($id, $attributes, $touch);
        $this->auditCustomNew = [
            $relationName => $this->{$relationName}()->get()->isEmpty() ? [] : $this->{$relationName}()->get()->toArray(),
        ];
        Event::dispatch(AuditCustom::class, [$this]);
        $this->isCustomEvent  = false;
    }

    /**
     * @param mixed $ids
     * @param bool  $touch
     *
     * @throws AuditingException
     *
     * @return int
     */
    public function auditDetach(string $relationName, $ids = null, $touch = true)
    {
        if (!method_exists($this, $relationName) || !method_exists($this->{$relationName}(), 'detach')) {
            throw new AuditingException('Relationship ' . $relationName . ' was not found or does not support method detach');
        }

        $this->auditEvent     = 'detach';
        $this->isCustomEvent  = true;
        $this->auditCustomOld = [
            $relationName => $this->{$relationName}()->get()->isEmpty() ? [] : $this->{$relationName}()->get()->toArray(),
        ];
        $results              = $this->{$relationName}()->detach($ids, $touch);
        $this->auditCustomNew = [
            $relationName => $this->{$relationName}()->get()->isEmpty() ? [] : $this->{$relationName}()->get()->toArray(),
        ];
        Event::dispatch(AuditCustom::class, [$this]);
        $this->isCustomEvent  = false;

        return empty($results) ? 0 : $results;
    }

    /**
     * @param \Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Model|array $ids
     * @param bool                                                                     $detaching
     * @param bool                                                                     $skipUnchanged
     * @param mixed                                                                    $relationName
     *
     * @throws AuditingException
     *
     * @return array
     */
    public function auditSync($relationName, $ids, $detaching = true)
    {
        if (!method_exists($this, $relationName) || !method_exists($this->{$relationName}(), 'sync')) {
            throw new AuditingException('Relationship ' . $relationName . ' was not found or does not support method sync');
        }

        $this->auditEvent     = 'sync';

        $this->auditCustomOld = [
            $relationName => $this->{$relationName}()->get()->isEmpty() ? [] : $this->{$relationName}()->get()->toArray(),
        ];

        $changes              = $this->{$relationName}()->sync($ids, $detaching);

        if (collect($changes)->flatten()->isEmpty()) {
            $this->auditCustomOld = [];
            $this->auditCustomNew = [];
        } else {
            $this->auditCustomNew = [
                $relationName => $this->{$relationName}()->get()->isEmpty() ? [] : $this->{$relationName}()->get()->toArray(),
            ];
        }

        $this->isCustomEvent  = true;
        Event::dispatch(AuditCustom::class, [$this]);
        $this->isCustomEvent  = false;

        return $changes;
    }

    /**
     * @param \Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Model|array $ids
     * @param bool                                                                     $skipUnchanged
     *
     * @throws AuditingException
     *
     * @return array
     */
    public function auditSyncWithoutDetaching(string $relationName, $ids)
    {
        if (!method_exists($this, $relationName) || !method_exists($this->{$relationName}(), 'syncWithoutDetaching')) {
            throw new AuditingException('Relationship ' . $relationName . ' was not found or does not support method syncWithoutDetaching');
        }

        return $this->auditSync($relationName, $ids, false);
    }
}
