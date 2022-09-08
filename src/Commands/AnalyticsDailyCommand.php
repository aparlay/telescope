<?php

namespace Aparlay\Core\Commands;

use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Models\Analytic;
use Aparlay\Core\Models\Email;
use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\MediaLike;
use Aparlay\Core\Models\MediaVisit;
use Illuminate\Console\Command;

class AnalyticsDailyCommand extends Command
{
    public $signature = 'analytics:daily';

    public $description = 'This command is responsible for creating Daily Analytics Report';

    public function handle()
    {
        $timestamp = strtotime('midnight');
        $startUtc = DT::timestampToUtc($timestamp);
        $endUtc = DT::timestampToUtc($timestamp + 86400);
        $availableMedia = Media::date(null, $endUtc)->count();
        $date = date('Y-m-d', $timestamp + 20000);
        $mediaVisitCounts = 0;

        foreach (MediaVisit::query()->dateString($date)->get() as $mediaVisits) {
            $mediaVisitCounts += count($mediaVisits->media_ids);
        }

        $analytics = [
            'date'  => $date,
            'media' => [
                'uploaded'      => Media::date($startUtc, $endUtc)->count(),
                'failed'        => Media::date($startUtc, $endUtc)->failed()->count(),
                'completed'     => Media::date($startUtc, $endUtc)->completed()->count(),
                'confirmed'     => Media::date($startUtc, $endUtc)->confirmed()->count(),
                'denied'        => Media::date($startUtc, $endUtc)->denied()->count(),
                'in_review'     => Media::date($startUtc, $endUtc)->inReview()->count(),
                'deleted'       => Media::date($startUtc, $endUtc)->isDeleted()->count(),
                'public'        => Media::date($startUtc, $endUtc)->public()->count(),
                'private'       => Media::date($startUtc, $endUtc)->private()->count(),
                'likes'         => MediaLike::query()->date($startUtc, $endUtc)->count(),
                'mean_likes'    => $availableMedia ? MediaLike::query()->date($startUtc, $endUtc)->count() / $availableMedia : 0,
                'visits'        => $mediaVisitCounts,
                'mean_visits'   => $availableMedia ? $mediaVisitCounts / $availableMedia : 0,
            ],
            'user'  => [
                'registered'    => User::date($startUtc, $endUtc)->count(),
                'login'         => 0,
                'verified'      => User::date($startUtc, $endUtc)->active()->count(),
                'duration'      => 0,
                'watched'       => 0,
            ],
            'email' => [
                'sent'          => Email::date($startUtc, $endUtc)->count(),
                'failed'        => Email::date($startUtc, $endUtc)->failed()->count(),
                'opened'        => Email::date($startUtc, $endUtc)->opened()->count(),
            ],
        ];

        if (($model = Analytic::where('date', $analytics['date'])->first()) === null) {
            $model = new Analytic();
        }
        $model->fill($analytics)->save();

        return self::SUCCESS;
    }
}
