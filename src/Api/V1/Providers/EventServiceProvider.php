<?php

namespace Aparlay\Core\Api\V1\Providers;

use Aparlay\Core\Api\V1\Models\Block;
use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Models\MediaLike;
use Aparlay\Core\Api\V1\Models\MediaVisit;
use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Models\Report;
use Aparlay\Core\Models\Model;
use Aparlay\Core\Observers\BlockObserver;
use Aparlay\Core\Observers\MediaLikeObserver;
use Aparlay\Core\Observers\MediaObserver;
use Aparlay\Core\Observers\MediaVisitObserver;
use Aparlay\Core\Observers\UserObserver;
use Aparlay\Core\Observers\ReportObserver;


class EventServiceProvider extends \Aparlay\Core\Providers\EventServiceProvider
{
    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Block::observe(BlockObserver::class);
        MediaLike::observe(MediaLikeObserver::class);
        Media::observe(MediaObserver::class);
        MediaVisit::observe(MediaVisitObserver::class);
        User::observe(UserObserver::class);
        Report::observe(ReportObserver::class);
        parent::boot();
    }
}
