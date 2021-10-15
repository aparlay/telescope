<?php

namespace Aparlay\Core\Admin\Providers;

use Aparlay\Core\Admin\Models\Media;
use Aparlay\Core\Admin\Observers\MediaObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AdminServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Paginator::useBootstrap();
        Media::observe(MediaObserver::class);
        // parent::boot();
    }
}
