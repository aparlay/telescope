<?php

namespace Aparlay\Core\Admin\Services;

use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Models\Analytic;
use Aparlay\Core\Models\Email;
use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\MediaComment;
use Aparlay\Core\Models\MediaLike;
use Aparlay\Core\Models\MediaVisit;
use Aparlay\Payment\Admin\Models\Tip;
use Aparlay\Payment\Models\Subscription;
use Aparlay\Payout\Models\Order;
use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;
use Google\ApiCore\ApiException;
use MongoDB\BSON\UTCDateTime;

final class AnalyticsCalculatorService
{
    private BetaAnalyticsDataClient $client;

    public function __construct()
    {
        putenv('GOOGLE_APPLICATION_CREDENTIALS='.config('app.google-analytics.key_file'));
        $this->client = new BetaAnalyticsDataClient();
    }

    /**
     * @param  UTCDateTime  $startAt
     * @param  UTCDateTime  $endAt
     * @param  bool         $saveResults
     *
     * @return array
     * @throws ApiException
     */
    public function calculateAnalytics(UTCDateTime $startAt, UTCDateTime $endAt, bool $saveResults = true): array
    {
        $availableMedia = Media::date(null, $endAt)->count();

        $date = date('Y-m-d', $startAt->toDateTime()->getTimestamp() + 20000);

        $mediaVisitCounts = 0;

        foreach (MediaVisit::dateString($date)->get() as $mediaVisits) {
            $mediaVisitCounts += count($mediaVisits->media_ids);
        }

        $analytics = [
            'date' => $date,
            'media' => [
                'uploaded' => Media::date($startAt, $endAt)->count(),
                'failed' => Media::date($startAt, $endAt)->failed()->count(),
                'completed' => Media::date($startAt, $endAt)->completed()->count(),
                'confirmed' => Media::date($startAt, $endAt)->confirmed()->count(),
                'denied' => Media::date($startAt, $endAt)->denied()->count(),
                'in_review' => Media::date($startAt, $endAt)->inReview()->count(),
                'deleted' => Media::date($startAt, $endAt)->isDeleted()->count(),
                'public' => Media::date($startAt, $endAt)->public()->count(),
                'private' => Media::date($startAt, $endAt)->private()->count(),
                'likes' => MediaLike::date($startAt, $endAt)->count(),
                'comments' => MediaComment::date($startAt, $endAt)->count(),
                'mean_likes' => $availableMedia ? MediaLike::date($startAt, $endAt)->count() / $availableMedia : 0,
                'visits' => $mediaVisitCounts,
                'mean_visits' => $availableMedia ? $mediaVisitCounts / $availableMedia : 0,
            ],
            'user' => [
                'registered' => User::date($startAt, $endAt)->count(),
                'login' => 0,
                'verified' => User::date($startAt, $endAt)->active()->count(),
                'duration' => 0,
                'watched' => 0,
            ],
            'email' => [
                'sent' => Email::date($startAt, $endAt)->count(),
                'failed' => Email::date($startAt, $endAt)->failed()->count(),
                'opened' => Email::date($startAt, $endAt)->opened()->count(),
            ],
            'payment' => [
                'orders' => Order::date($startAt, $endAt)->count(),
                'orders_amount' => Order::date($startAt, $endAt)->sum('amount'),
                'subscriptions' => Subscription::date($startAt, $endAt)->count(),
                'subscriptions_amount' => Subscription::date($startAt, $endAt)->sum('amount'),
                'tips' => Tip::date($startAt, $endAt)->count(),
                'tips_amount' => Tip::date($startAt, $endAt)->sum('amount'),
            ],
            'google_analytics' => [
                'active_users' => $this->reportActiveUsers(
                    date('Y-m-d', $startAt->toDateTime()->getTimestamp()),
                    date('Y-m-d', $endAt->toDateTime()->getTimestamp())
                ),
                'new_users' => $this->reportNewUsers(
                    date('Y-m-d', $startAt->toDateTime()->getTimestamp()),
                    date('Y-m-d', $endAt->toDateTime()->getTimestamp())
                ),
                'total_users' => $this->reportTotalUsers(
                    date('Y-m-d', $startAt->toDateTime()->getTimestamp()),
                    date('Y-m-d', $endAt->toDateTime()->getTimestamp())
                ),
                'engagements' => $this->reportUserEngagementDuration(
                    date('Y-m-d', $startAt->toDateTime()->getTimestamp()),
                    date('Y-m-d', $endAt->toDateTime()->getTimestamp())
                ),
            ],
        ];

        if ($saveResults) {
            Analytic::query()->updateOrCreate([
                'date' => $analytics['date'],
            ], $analytics);
        }

        return $analytics;
    }

    /**
     * @param $from
     * @param $to
     *
     * @return array
     * @throws ApiException
     */
    public function reportActiveUsers($from, $to): array
    {
        return $this->getMetricResults('activeUsers', $from, $to);
    }

    /**
     * @param $from
     * @param $to
     *
     * @return array
     * @throws ApiException
     */
    public function reportNewUsers($from, $to): array
    {
        return $this->getMetricResults('newUsers', $from, $to);
    }

    /**
     * @param $from
     * @param $to
     *
     * @return array
     * @throws ApiException
     */
    public function reportTotalUsers($from, $to): array
    {
        return $this->getMetricResults('totalUsers', $from, $to);
    }

    /**
     * @param $from
     * @param $to
     *
     * @return array
     * @throws ApiException
     */
    public function reportUserEngagementDuration($from, $to): array
    {
        return $this->getMetricResults('userEngagementDuration', $from, $to);
    }

    /**
     * @param  string  $metric
     * @param  string  $from
     * @param  string  $to
     *
     * @return array
     * @throws ApiException
     */
    private function getMetricResults(string $metric, string $from, string $to): array
    {
        $response = $this->client->runReport([
            'property' => 'properties/'.config('app.google-analytics.property_id'),
            'dateRanges' => [
                new DateRange([
                    'start_date' => $from,
                    'end_date' => $to,
                ]),
            ],
            'dimensions' => [
                new Dimension(
                    [
                        'name' => 'country',
                    ]
                ),
            ],
            'metrics' => [
                new Metric(
                    [
                        'name' => $metric,
                    ]
                ),
            ],
        ]);

        $countries = [];
        $total = 0;
        foreach ($response->getRows() as $row) {
            foreach ($row->getDimensionValues() as $key => $dimensionValue) {
                $countries[$dimensionValue->getValue()] = $row->getMetricValues()[$key]->getValue();
                $total += $row->getMetricValues()[$key]->getValue();
            }
        }

        return ['countries' => $countries, 'total' => $total];
    }

}
