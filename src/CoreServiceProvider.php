<?php

namespace Aparlay\Core;

use Aparlay\Core\Admin\Providers\AdminServiceProvider;
use Aparlay\Core\Admin\Providers\EventServiceProvider;
use Aparlay\Core\Api\V1\Providers\AuthServiceProvider;
use Aparlay\Core\Commands\AdminSyncLiveWireComponents;
use Aparlay\Core\Commands\AnalyticsDailyCommand;
use Aparlay\Core\Commands\AnalyticsTwoMonthCommand;
use Aparlay\Core\Commands\CleanupCommand;
use Aparlay\Core\Commands\CoreCommand;
use Aparlay\Core\Commands\RoleCommand;
use Aparlay\Core\Commands\VideoReprocessCommand;
use Aparlay\Core\Commands\VideoScoreCommand;
use Aparlay\Core\Commands\VideoScoreDailyCommand;
use Aparlay\Core\Commands\VideoScoreHourlyCommand;
use Aparlay\Core\Commands\VideoUpdateInfoCommand;
use Aparlay\Core\Commands\WsCommand;
use Aparlay\Core\Helpers\ConfigHelper;
use Aparlay\Core\Helpers\IP;
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
        $this->app->register(AdminServiceProvider::class);
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
                __DIR__.'/../public/admin' => public_path('admin'),
            ], 'public');

            $this->commands([
                CoreCommand::class,
                RoleCommand::class,
                VideoReprocessCommand::class,
                VideoScoreCommand::class,
                VideoScoreDailyCommand::class,
                VideoScoreHourlyCommand::class,
                VideoUpdateInfoCommand::class,
                WsCommand::class,
                AnalyticsTwoMonthCommand::class,
                AnalyticsDailyCommand::class,
                CleanupCommand::class,
                AdminSyncLiveWireComponents::class
            ]);
        } else {
            app()->make(\Aparlay\Core\Api\V1\Http\Kernel::class);
            app()->make(\Aparlay\Core\Admin\Http\Kernel::class);
        }

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

        ConfigHelper::loadDbConfig();
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: IP::trueAddress());
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
