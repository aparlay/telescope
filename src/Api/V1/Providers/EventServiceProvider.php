<?php

namespace Aparlay\Core\Api\V1\Providers;

use Aparlay\Core\Api\V1\Models\Block;
use Aparlay\Core\Api\V1\Models\Follow;
use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Models\MediaLike;
use Aparlay\Core\Api\V1\Models\MediaVisit;
use Aparlay\Core\Api\V1\Models\Report;
use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Models\Note;
use Aparlay\Core\Events\DispatchAuthenticatedEndpoints;
use Aparlay\Core\Events\GenerateNote;
use Aparlay\Core\Listeners\AddNote;
use Aparlay\Core\Listeners\LogAuthenticated;
use Aparlay\Core\Listeners\OnlineUsers;
use Aparlay\Core\Observers\BlockObserver;
use Aparlay\Core\Observers\FollowObserver;
use Aparlay\Core\Observers\MediaLikeObserver;
use Aparlay\Core\Observers\MediaObserver;
use Aparlay\Core\Observers\MediaVisitObserver;
use Aparlay\Core\Observers\ReportObserver;
use Aparlay\Core\Observers\UserObserver;
use Aparlay\Core\Observers\NoteObserver;
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
        DispatchAuthenticatedEndpoints::class => [
            LogAuthenticated::class,
            OnlineUsers::class,
        ],
        GenerateNote::class => [
            AddNote::class,
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
        Note::observe(NoteObserver::class);
        parent::boot();
    }
}
