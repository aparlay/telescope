<?php

namespace Aparlay\Core\Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Artisan;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app           = require getcwd() . '/bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        Artisan::call('config:clear');
        $app->make('config')->set('app.url', env('TEST_DOMAIN'));
        $app->make('config')->set('app.is_testing', true);
        $app->make('config')->set('app.debug', false);
        $app->make('config')->set('profiler.enabled', false);
        $app->make('config')->set('database.default', 'testing');
        $app->make('config')->set('database.default', 'testing');
        $this->baseUrl = env('TEST_DOMAIN');

        return $app;
    }
}
