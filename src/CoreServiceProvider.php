<?php

namespace Aparlay\Core;

use Aparlay\Core\Commands\CoreCommand;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class CoreServiceProvider extends ServiceProvider
{
    protected $defer = false;
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->isLocal()) {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
        $this->mergeConfigFrom(__DIR__ . '/../config/core.php', 'core');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/core.php' => config_path('core.php'),
            ], 'config');

            $this->publishes([
                __DIR__ . '/../resources/views/admin' => base_path('resources/views/admin'),
            ], 'views');

            $this->commands([
                CoreCommand::class,
            ]);
        }
        $this->configureRateLimiting();

        include __DIR__ . '/../routes/api.php';
        include __DIR__ . '/../routes/web.php';
        include __DIR__ . '/../routes/admin.php';
        include __DIR__ . '/../routes/channels.php';
        include __DIR__ . '/../routes/console.php';
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
