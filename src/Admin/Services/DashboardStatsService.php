<?php

namespace Aparlay\Core\Admin\Services;

use Aparlay\Core\Admin\Models\Analytic;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Jenssegers\Mongodb\Collection;

final class DashboardStatsService
{
    public function getAnalyticStats(?Carbon $from, ?Carbon $to)
    {
        $value = Analytic::query()->raw(function (Collection $collection) use ($from, $to) {
            $aggregations = [];

            if (isset($from) && isset($to)) {
                $aggregations[] = [
                    '$match' => [
                        'date' => [
                            '$gte' => $from->format('Y-m-d'),
                            '$lte' => $to->format('Y-m-d'),
                        ],
                    ],
                ];
            }

            $aggregations[] = [
                    '$group' => [
                        '_id' => 0,
                        'media_uploaded' => ['$sum' => '$media.uploaded'],
                        'media_failed' => ['$sum' => '$media.failed'],
                        'media_completed' => ['$sum' => '$media.completed'],
                        'media_confirmed' => ['$sum' => '$media.confirmed'],
                        'media_in_review' => ['$sum' => '$media.in_review'],
                        'media_deleted' => ['$sum' => '$media.deleted'],
                        'media_public' => ['$sum' => '$media.public'],
                        'media_private' => ['$sum' => '$media.private'],
                        'media_likes' => ['$sum' => '$media.likes'],
                        'media_comments' => ['$sum' => '$media.comments'],
                        'media_mean_likes' => ['$sum' => '$media.mean_likes'],
                        'media_visits' => ['$sum' => '$media.visits'],
                        'media_mean_visits' => ['$sum' => '$media.mean_visits'],

                        'email_sent' => ['$sum' => '$email.sent'],
                        'email_failed' => ['$sum' => '$email.failed'],
                        'email_opened' => ['$sum' => '$email.opened'],

                        'user_registered' => ['$sum' => '$user.registered'],
                        'user_login' => ['$sum' => '$user.login'],
                        'user_verified' => ['$sum' => '$user.verified'],
                        'user_duration' => ['$sum' => '$user.duration'],
                        'user_watched' => ['$sum' => '$user.watched'],

                        'payment_orders' => ['$sum' => '$payment.orders'],
                        'payment_orders_amount' => ['$sum' => '$payment.orders_amount'],
                        'payment_subscriptions' => ['$sum' => '$payment.subscriptions'],
                        'payment_subscriptions_amount' => ['$sum' => '$payment.subscriptions_amount'],
                        'payment_tips' => ['$sum' => '$payment.tips'],
                        'payment_tips_amount' => ['$sum' => '$payment.tips_amount'],

                        'google_analytics_active_users' => ['$sum' => '$google_analytics.active_users.total'],
                        'google_analytics_new_users' => ['$sum' => '$google_analytics.new_users.total'],
                        'google_analytics_total_users' => ['$sum' => '$google_analytics.total_users.total'],
                        'google_analytics_engagement' => ['$sum' => '$google_analytics.engagements.total'],
                    ],
            ];

            return $collection->aggregate($aggregations);
        });

        if (method_exists($value, 'toArray')) {
            return Arr::first($value->toArray()) ?? [];
        }

        return [];
    }
}
