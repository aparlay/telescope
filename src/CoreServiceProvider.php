<?php

namespace Aparlay\Core;

use Aparlay\Core\Api\V1\Http\Kernel;
use Aparlay\Core\Api\V1\Providers\AuthServiceProvider;
use Aparlay\Core\Api\V1\Providers\EventServiceProvider;
use Aparlay\Core\Commands\CoreCommand;
use Aparlay\Core\Commands\WsCommand;
use App\Providers\TelescopeServiceProvider;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
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
            $this->app->register(IdeHelperServiceProvider::class);
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
        $this->app->register(AuthServiceProvider::class);
        $this->app->register(EventServiceProvider::class);

        $this->mergeConfig();
    }

    /**
     * Bootstrap services.
     *
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/core.php' => config_path('core.php'),
            ], 'config');

            $this->publishes([
                __DIR__.'/../resources/views/admin' => base_path('resources/views/admin'),
            ], 'views');

            $this->commands([
                CoreCommand::class,
                WsCommand::class,
            ]);
        }

        app()->make(Kernel::class);

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'default_view');

        $this->configureRateLimiting();

        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/admin.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/channels.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/console.php');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'core');
        $this->publishConfig();
        $this->publishMigrations();
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

    private function mergeConfig()
    {
        $path = $this->getConfigPath();
        $this->mergeConfigFrom($path, 'core');
    }

    private function publishConfig()
    {
        $path = $this->getConfigPath();
        $this->publishes([$path => config_path('core.php')], 'config');
    }

    private function publishMigrations()
    {
        $path = $this->getMigrationsPath();
        $this->publishes([$path => database_path('migrations')], 'migrations');
    }

    private function getConfigPath()
    {
        return __DIR__.'/../config/core.php';
    }

    private function getMigrationsPath()
    {
        return __DIR__.'/../database/migrations';
    }
}
