<?php

namespace Aparlay\Core\Admin\Services;

use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Models\ActiveUser;
use Aparlay\Core\Models\Analytic;
use Aparlay\Core\Models\Email;
use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\MediaComment;
use Aparlay\Core\Models\MediaLike;
use Aparlay\Core\Models\MediaVisit;
use Aparlay\Payment\Admin\Models\Tip;
use Aparlay\Payment\Models\Subscription;
use Aparlay\Payout\Models\Order;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;
use Google\ApiCore\ApiException;
use Illuminate\Support\Facades\Redis;
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
     * @throws \RedisException
     */
    public function calculateAnalytics(
        UTCDateTime $startAt,
        UTCDateTime $endAt,
        bool $saveResults = true,
        bool $ga = false): array
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
                'active' => User::date($startAt, $endAt)->active()->count(),
                'login' => User::date($startAt, $endAt, 'last_online_at')->count(),
                'verified' => User::date($startAt, $endAt)->idVerified()->count(),
                'unique' => Redis::sCard('tracking:users:unique:'.date('Y:m:d', $startAt->toDateTime()->getTimestamp() + 20000)),
                'returned' => Redis::sCard('tracking:users:returned:'.date('Y:m:d', $startAt->toDateTime()->getTimestamp() + 20000)),
                'duration' => (int) Redis::get('tracking:media:duration:'.date('Y:m:d', $startAt->toDateTime()->getTimestamp() + 20000)),
                'watched' => (int) Redis::get('tracking:media:watched:'.date('Y:m:d', $startAt->toDateTime()->getTimestamp() + 20000), 0),
            ],
            'email' => [
                'sent' => Email::query()->date($startAt, $endAt)->sent()->count(),
                'failed' => Email::query()->date($startAt, $endAt)->failed()->count(),
                'delivered' => Email::query()->date($startAt, $endAt)->delivered()->count(),
                'deferred' => Email::query()->date($startAt, $endAt)->deferred()->count(),
                'bounced' => Email::query()->date($startAt, $endAt)->bounced()->count(),
            ],
            'payment' => [
                'orders' => Order::query()->date($startAt, $endAt)->count(),
                'orders_amount' => Order::query()->date($startAt, $endAt)->sum('amount'),
                'subscriptions' => Subscription::query()->date($startAt, $endAt)->count(),
                'subscriptions_amount' => Subscription::query()->date($startAt, $endAt)->sum('amount'),
                'tips' => Tip::query()->date($startAt, $endAt)->count(),
                'tips_amount' => Tip::query()->date($startAt, $endAt)->sum('amount'),
            ],
        ];

        if ($ga) {
            $analytics['google_analytics'] = [
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
            ];
        }

        if ($saveResults) {
            Analytic::query()->updateOrCreate([
                'date' => $analytics['date'],
            ], $analytics);
        }

        $this->storeActiveUsers(
            date('Y-m-d', $startAt->toDateTime()->getTimestamp()),
            date('Y-m-d', $endAt->toDateTime()->getTimestamp())
        );

        return $analytics;
    }

    /**
     * @param $from
     * @param $to
     *
     * @return void
     */
    public function storeActiveUsers($from, $to): void
    {
        foreach (CarbonPeriod::create($from, $to) as $date) {
            /** @var Carbon $date */
            $data = [];
            $returnedCacheKey = 'tracking:users:returned:'.date('Y:m:d', $date->getTimestamp());
            if (Redis::exists($returnedCacheKey)) {
                $dateString = date('Y-m-d', $date->getTimestamp());
                $storedUuids = ActiveUser::query()->stringDate($dateString)->select(['uuid'])->pluck('uuid')->toArray();
                $freshUuids = array_diff(Redis::smembers($returnedCacheKey), $storedUuids);
                foreach ($freshUuids as $uuid) {
                    $data[] = ['date' => $dateString, 'uuid' => $uuid, 'created_at' => new UTCDateTime()];
                }
            }

            if (! empty($data)) {
                ActiveUser::insert($data);
            }
        }
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
