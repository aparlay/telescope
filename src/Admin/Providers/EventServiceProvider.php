<?php

namespace Aparlay\Core\Admin\Providers;

use Aparlay\Core\Admin\Models\Alert;
use Aparlay\Core\Admin\Models\Media;
use Aparlay\Core\Admin\Models\MediaComment;
use Aparlay\Core\Admin\Models\Setting;
use Aparlay\Core\Admin\Models\User;
use Aparlay\Core\Admin\Observers\AlertObserver;
use Aparlay\Core\Admin\Observers\SettingObserver;
use Aparlay\Core\Listeners\CreateTipMediaComment;
use Aparlay\Core\Observers\MediaCommentObserver;
use Aparlay\Core\Observers\MediaObserver;
use Aparlay\Core\Observers\UserObserver;
use Aparlay\Payment\Events\TipCreatedEvent;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
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

        TipCreatedEvent::class => [
            CreateTipMediaComment::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Alert::observe(AlertObserver::class);
        User::observe(UserObserver::class);
        Media::observe(MediaObserver::class);
        MediaComment::observe(MediaCommentObserver::class);
        Setting::observe(SettingObserver::class);
        parent::boot();
    }
}
