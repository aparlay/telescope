<?php

namespace Aparlay\Core\Providers;

use Aparlay\Core\Events\UserOtpRequestedEvent;
use Aparlay\Core\Listeners\SendOtpToUserListener;
use Aparlay\Core\Models\BaseModel;
use Aparlay\Core\Models\Block;
use Aparlay\Core\Models\Follow;
use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\MediaComment;
use Aparlay\Core\Models\MediaLike;
use Aparlay\Core\Models\MediaVisit;
use Aparlay\Core\Models\Report;
use Aparlay\Core\Models\User;
use Aparlay\Core\Observers\BaseModelObserver;
use Aparlay\Core\Observers\BlockObserver;
use Aparlay\Core\Observers\FollowObserver;
use Aparlay\Core\Observers\MediaCommentObserver;
use Aparlay\Core\Observers\MediaLikeObserver;
use Aparlay\Core\Observers\MediaObserver;
use Aparlay\Core\Observers\MediaVisitObserver;
use Aparlay\Core\Observers\ReportObserver;
use Aparlay\Core\Observers\UserObserver;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        UserOtpRequestedEvent::class => [
            SendOtpToUserListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        BaseModel::observe(BaseModelObserver::class);

        Follow::observe(FollowObserver::class);
        Block::observe(BlockObserver::class);
        MediaLike::observe(MediaLikeObserver::class);
        MediaComment::observe(MediaCommentObserver::class);
        Media::observe(MediaObserver::class);
        MediaVisit::observe(MediaVisitObserver::class);
        User::observe(UserObserver::class);
        Report::observe(ReportObserver::class);

        parent::boot();
    }
}
