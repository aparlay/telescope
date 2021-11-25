<?php

namespace Aparlay\Core\Admin\Providers;

use Aparlay\Core\Admin\Models\Alert;
use Aparlay\Core\Admin\Models\Media;
use Aparlay\Core\Admin\Models\Setting;
use Aparlay\Core\Admin\Models\User;
use Aparlay\Core\Admin\Observers\AlertObserver;
use Aparlay\Core\Admin\Observers\SettingObserver;
use Aparlay\Core\Observers\MediaObserver;
use Aparlay\Core\Observers\UserObserver;

class EventServiceProvider extends \Aparlay\Core\Providers\EventServiceProvider
{
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
        parent::boot();
    }
}
