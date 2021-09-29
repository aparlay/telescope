<?php

namespace Aparlay\Core\Admin\Providers;

use Aparlay\Core\Admin\Repositories\Analytic\AnalyticRepository;
use Aparlay\Core\Admin\Repositories\Analytic\AnalyticRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AdminRepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register Repositories.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(AnalyticRepositoryInterface::class, AnalyticRepository::class);
    }
}
