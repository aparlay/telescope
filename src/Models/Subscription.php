<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Models\Queries\SubscriptionQueryBuilder;
use Illuminate\Database\Eloquent\Builder;

final class Subscription extends BaseModel
{
    /**
     * The collection associated with the model.
     *
     * @var string
     */
    protected $collection = 'payment_subscriptions';

    /**
     * @var string[]
     */
    protected $fillable = [
        '_id',
    ];

    /**
     * @return SubscriptionQueryBuilder|Builder
     */
    public static function query(): SubscriptionQueryBuilder|Builder
    {
        return parent::query();
    }

    /**
     * @param $query
     * @return SubscriptionQueryBuilder
     */
    public function newEloquentBuilder($query): SubscriptionQueryBuilder
    {
        return new SubscriptionQueryBuilder($query);
    }
}
