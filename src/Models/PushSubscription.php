<?php

namespace Aparlay\Core\Models;
/**
 * @property string $endpoint
 * @property string|null $public_key
 * @property string|null $auth_token
 * @property string|null $content_encoding
 * @property \Illuminate\Database\Eloquent\Model $subscribable
 */
class PushSubscription extends BaseModel
{
    /**
     * The collection associated with the model.
     *
     * @var string
     */
    protected $collection = 'push_subscriptions';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'endpoint',
        'public_key',
        'auth_token',
        'content_encoding',
    ];

    /**
     * Get the model related to the subscription.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function subscribable()
    {
        return $this->morphTo();
    }

    /**
     * Find a subscription by the given endpint.
     *
     * @param  string  $endpoint
     * @return static|null
     */
    public static function findByEndpoint($endpoint)
    {
        return static::where('endpoint', $endpoint)->first();
    }
}
