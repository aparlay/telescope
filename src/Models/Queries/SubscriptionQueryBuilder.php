<?php

namespace Aparlay\Core\Models\Queries;

use Aparlay\Core\Helpers\DT;
use Aparlay\Payment\Models\Enums\SubscriptionStatus;

class SubscriptionQueryBuilder extends EloquentQueryBuilder
{
    use SimpleUserCreatorQuery;

    /**
     * This is different from active() because it also includes cancelled(not expired) subscriptions.
     * @return SubscriptionQueryBuilder
     * @return self
     */
    public function valid()
    {
        throw new \Exception('Not used yet');

        return $this
            ->where('status', [
                '$in' => [
                    SubscriptionStatus::ACTIVE->value,
                    SubscriptionStatus::CANCELED->value,
                ],
            ])
            ->where('expired_at', '>', DT::utcNow());
    }
}
