<?php

namespace Aparlay\Core\Commands;

use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Models\MediaLike;
use Aparlay\Core\Api\V1\Models\MediaVisit;
use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Microservices\ws\WsChannel;
use Aparlay\Core\Microservices\ws\WsDispatcherFactory;
use Aparlay\Core\Models\Analytic;
use Aparlay\Core\Models\Email;
use Illuminate\Console\Command;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Swoole\Coroutine\Http\Client;
use Swoole\Runtime;
use Swoole\Timer;
use Swoole\WebSocket\Frame;

// use yii\console\ExitCode;


class AnalyticsCommand extends Command
{
    public $signature = 'report:alalytics';

    public $description = 'Aparlay Ws Client';

    public function handle()
    {
    }

    public function actionTwoMonth()
    {
        for ($i = -90; $i <= 0; $i++) {
            
            $timestamp          = strtotime($i . ' days midnight');
            $startUtc           = DT::timestampToUtc($timestamp);
            $endUtc             = DT::timestampToUtc($timestamp + 86400);
            $availableMedia     = Media::find()->date(null, $endUtc)->count();
            $date               = date('Y-m-d', $timestamp + 20000);
            $mediaVisitCounts   = 0;

            foreach (MediaVisit::find()->date($date)->each() as $mediaVisits) {
                $mediaVisitCounts += count($mediaVisits->media_ids);
            }
            $analytics = [
                'date'  => $date,
                'media' => [
                    'uploaded'      => Media::find()->date($startUtc, $endUtc)->count(),
                    'failed'        => Media::find()->date($startUtc, $endUtc)->failed()->count(),
                    'completed'     => Media::find()->date($startUtc, $endUtc)->completed()->count(),
                    'confirmed'     => Media::find()->date($startUtc, $endUtc)->confirmed()->count(),
                    'denied'        => Media::find()->date($startUtc, $endUtc)->denied()->count(),
                    'in_review'     => Media::find()->date($startUtc, $endUtc)->inReview()->count(),
                    'deleted'       => Media::find()->date($startUtc, $endUtc)->deleted()->count(),
                    'public'        => Media::find()->date($startUtc, $endUtc)->public()->count(),
                    'private'       => Media::find()->date($startUtc, $endUtc)->private()->count(),
                    'likes'         => MediaLike::find()->date($startUtc, $endUtc)->count(),
                    'mean_likes'    => $availableMedia ? MediaLike::find()->date($startUtc, $endUtc)->count() / $availableMedia : 0,
                    'visits'        => $mediaVisitCounts,
                    'mean_visits'   => $availableMedia ? $mediaVisitCounts / $availableMedia : 0,
                ],
                'user'  => [
                    'registered'    => User::find()->date($startUtc, $endUtc)->count(),
                    'login'         => 0,
                    'verified'      => User::find()->date($startUtc, $endUtc)->active()->count(),
                    'duration'      => 0,
                    'watched'       => 0,
                ],
                'email' => [
                    'sent'          => Email::find()->date($startUtc, $endUtc)->count(),
                    'failed'        => Email::find()->date($startUtc, $endUtc)->failed()->count(),
                    'opened'        => Email::find()->date($startUtc, $endUtc)->opened()->count(),
                ],
            ];

            if (($model = Analytic::find()->andWhere(['date' => $analytics['date']])->one()) === null) {
                $model = new Analytic();
            }
            $model->attributes = $analytics;
            $model->save();


            $this->stdout($date . ' analytics stored.' . PHP_EOL, Console::BOLD, Console::FG_YELLOW);
        }

        // return ExitCode::OK;
        return Response::HTTP_OK;
    }

    public function actionDaily()
    {
        $timestamp          = strtotime('midnight');
        $startUtc           = DT::timestampToUtc($timestamp);
        $endUtc             = DT::timestampToUtc($timestamp + 86400);
        $availableMedia     = Media::find()->date(null, $endUtc)->count();
        $date               = date('Y-m-d', $timestamp + 20000);
        $mediaVisitCounts   = 0;

        foreach (MediaVisit::find()->date($date)->each() as $mediaVisits) {
            $mediaVisitCounts += count($mediaVisits->media_ids);
        }

        $analytics = [
            'date'  => $date,
            'media' => [
                'uploaded'      => Media::find()->date($startUtc, $endUtc)->count(),
                'failed'        => Media::find()->date($startUtc, $endUtc)->failed()->count(),
                'completed'     => Media::find()->date($startUtc, $endUtc)->completed()->count(),
                'confirmed'     => Media::find()->date($startUtc, $endUtc)->confirmed()->count(),
                'denied'        => Media::find()->date($startUtc, $endUtc)->denied()->count(),
                'in_review'     => Media::find()->date($startUtc, $endUtc)->inReview()->count(),
                'deleted'       => Media::find()->date($startUtc, $endUtc)->deleted()->count(),
                'public'        => Media::find()->date($startUtc, $endUtc)->public()->count(),
                'private'       => Media::find()->date($startUtc, $endUtc)->private()->count(),
                'likes'         => MediaLike::find()->date($startUtc, $endUtc)->count(),
                'mean_likes'    => $availableMedia ? MediaLike::find()->date($startUtc, $endUtc)->count() / $availableMedia : 0,
                'visits'        => $mediaVisitCounts,
                'mean_visits'   => $availableMedia ? $mediaVisitCounts / $availableMedia : 0,
            ],
            'user'  => [
                'registered'    => User::find()->date($startUtc, $endUtc)->count(),
                'login'         => 0,
                'verified'      => User::find()->date($startUtc, $endUtc)->active()->count(),
                'duration'      => 0,
                'watched'       => 0,
            ],
            'email' => [
                'sent'          => Email::find()->date($startUtc, $endUtc)->count(),
                'failed'        => Email::find()->date($startUtc, $endUtc)->failed()->count(),
                'opened'        => Email::find()->date($startUtc, $endUtc)->opened()->count(),
            ],
        ];

        if (($model = Analytic::find()->andWhere(['date' => $analytics['date']])->one()) === null) {
            $model = new Analytic();
        }
        $model->attributes = $analytics;
        $model->save();

        // return ExitCode::OK;
        return Response::HTTP_OK;
    }
}
