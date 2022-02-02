<?php

namespace Aparlay\Core\Admin\Providers;

use Aparlay\Core\Admin\Models\Alert;
use Aparlay\Core\Admin\Models\Media;
use Aparlay\Core\Admin\Models\Note;
use Aparlay\Core\Admin\Models\Setting;
use Aparlay\Core\Admin\Models\User;
use Aparlay\Core\Admin\Observers\AlertObserver;
use Aparlay\Core\Admin\Observers\SettingObserver;
use Aparlay\Core\Events\GenerateNote;
use Aparlay\Core\Listeners\AddNote;
use Aparlay\Core\Observers\MediaObserver;
use Aparlay\Core\Observers\NoteObserver;
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
    ];
    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Alert::observe(new AlertObserver());
        User::observe(new UserObserver());
        Media::observe(new MediaObserver());
        Setting::observe(new SettingObserver());
        Note::observe(new NoteObserver());
        parent::boot();
    }
}
