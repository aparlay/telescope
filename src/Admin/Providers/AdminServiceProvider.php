<?php

namespace Aparlay\Core\Admin\Providers;

use Aparlay\Core\Admin\Resources\MediaResource;
use Aparlay\Core\Admin\Resources\UserResource;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AdminServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Paginator::useBootstrap();
        MediaResource::withoutWrapping();
        UserResource::withoutWrapping();
    }
}
