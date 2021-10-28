<?php

namespace Aparlay\Core\Admin\Providers;

use Aparlay\Core\Admin\Models\Alert;
use Aparlay\Core\Observers\BaseModelObserver;

class EventServiceProvider extends \Aparlay\Core\Providers\EventServiceProvider
{
    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Alert::observe(new BaseModelObserver());
        parent::boot();
    }
}
