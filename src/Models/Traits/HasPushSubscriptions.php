<?php

namespace Aparlay\Core\Models\Traits;

use Aparlay\Core\Models\PushSubscription;
use Illuminate\Support\Str;
use MongoDB\BSON\ObjectId;

trait HasPushSubscriptions
{
    /**
     *  Get all of the subscriptions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function pushSubscriptions()
    {
        return $this->morphMany(PushSubscription::class, 'entity.');
    }

    /**
     * Update (or create) subscription.
     *
     * @param  string  $endpoint
     * @param  string|null  $key
     * @param  string|null  $token
     * @param  string|null  $contentEncoding
     * @return PushSubscription
     */
    public function updatePushSubscription($endpoint, $key = null, $token = null, $contentEncoding = null)
    {
        $subscription = PushSubscription::findByEndpoint($endpoint);

        if ($subscription && $this->ownsPushSubscription($subscription)) {
            $subscription->public_key = $key;
            $subscription->auth_token = $token;
            $subscription->content_encoding = $contentEncoding;
            $subscription->save();

            return $subscription;
        }

        if ($subscription && ! $this->ownsPushSubscription($subscription)) {
            $subscription->delete();
        }

        return PushSubscription::create([
            'endpoint' => $endpoint,
            'public_key' => $key,
            'auth_token' => $token,
            'content_encoding' => $contentEncoding,
            'entity._id' => new ObjectId($this->_id),
            'entity._type' => Str::afterLast($this->getMorphClass(), '\\'),
        ]);
    }

    /**
     * Determine if the model owns the given subscription.
     *
     * @param  PushSubscription  $subscription
     * @return bool
     */
    public function ownsPushSubscription($subscription)
    {
        return (string) $subscription->entity['_id'] === (string) $this->getKey() &&
                        $subscription->entity['_type'] === Str::afterLast($this->getMorphClass(), '\\');
    }

    /**
     * Delete subscription by endpoint.
     *
     * @param  string  $endpoint
     * @return void
     */
    public function deletePushSubscription($endpoint)
    {
        $this->pushSubscriptions()->where('endpoint', $endpoint)->delete();
    }

    /**
     * Get all of the subscriptions.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function routeNotificationForWebPush()
    {
        return PushSubscription::query()
            ->where('entity._id', new ObjectId($this->_id))
            ->where('entity._type', Str::afterLast($this->getMorphClass(), '\\'))
            ->get();
    }
}
