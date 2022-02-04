<?php

namespace Aparlay\Core\Api\V1\Providers;

use Aparlay\Core\Api\V1\Models\Block;
use Aparlay\Core\Api\V1\Models\Follow;
use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Models\MediaLike;
use Aparlay\Core\Api\V1\Models\MediaVisit;
use Aparlay\Core\Api\V1\Models\Report;
use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Events\DispatchAuthenticatedEndpointsEvent;
use Aparlay\Core\Listeners\LogAuthenticatedListener;
use Aparlay\Core\Listeners\OnlineUsersListener;
use Aparlay\Core\Observers\BlockObserver;
use Aparlay\Core\Observers\FollowObserver;
use Aparlay\Core\Observers\MediaLikeObserver;
use Aparlay\Core\Observers\MediaObserver;
use Aparlay\Core\Observers\MediaVisitObserver;
use Aparlay\Core\Observers\ReportObserver;
use Aparlay\Core\Observers\UserObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;

class EventServiceProvider extends \Aparlay\Core\Providers\EventServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        DispatchAuthenticatedEndpointsEvent::class => [
            LogAuthenticatedListener::class,
            OnlineUsersListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Follow::observe(FollowObserver::class);
        Block::observe(BlockObserver::class);
        MediaLike::observe(MediaLikeObserver::class);
        Media::observe(MediaObserver::class);
        MediaVisit::observe(MediaVisitObserver::class);
        User::observe(UserObserver::class);
        Report::observe(ReportObserver::class);
        parent::boot();
    }
}
