<?php

namespace Aparlay\Core\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as BaseServiceProvider;

class RepositoryServiceProvider extends BaseServiceProvider
{
    public function register()
    {
        $this->app->bind(
            'Aparlay\Core\Repositories\Interfaces\MediaRepositoryInterface',
            'Aparlay\Core\Repositories\MediaRepository'
        );
    }
}
