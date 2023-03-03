<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Api\V1\Models\User;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Relation;

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

    public static function boot()
    {
        parent::boot();

        // to keep entities in database without namespace
        Relation::morphMap([
            'User' => User::class,
        ]);
    }

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
        'entity._id',
        'entity._type',
    ];

    /**
     * Get the model related to the subscription.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function entityObj(): MorphTo|\Jenssegers\Mongodb\Relations\MorphTo
    {
        return $this->morphTo('entity.');
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
