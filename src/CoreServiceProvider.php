<?php

namespace Aparlay\Core;

use Aparlay\Core\Commands\CoreCommand;
use Aparlay\Core\Commands\WsCommand;
use Aparlay\Core\Pagination\CoreCursorPaginator;
use Aparlay\Core\Providers\EventServiceProvider;
use App\Providers\TelescopeServiceProvider;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Jenssegers\Mongodb\Eloquent\Builder;

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
            $this->app->register(EventServiceProvider::class);
        }

        $this->mergeConfigFrom(__DIR__.'/../config/core.php', 'core');
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

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'default_view');

        $this->configureRateLimiting();

        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/admin.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/channels.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/console.php');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'core');

        Builder::macro('cursorPaginate', function ($limit, $columns) {
            $cursor = CoreCursorPaginator::currentCursor();

            if ($cursor) {
                $apply = function ($query, $columns, $cursor) use (&$apply) {
                    $query->where(function ($query) use ($columns, $cursor, $apply) {
                        $column = key($columns);
                        $direction = array_shift($columns);
                        $value = array_shift($cursor);

                        $query->where($column, $direction === 'asc' ? '>' : '<', $value);

                        if (! empty($columns)) {
                            $query->orWhere($column, $value);
                            $apply($query, $columns, $cursor);
                        }
                    });
                };

                $apply($this, $columns, $cursor);
            }

            foreach ($columns as $column => $direction) {
                $this->orderBy($column, $direction);
            }

            $items = $this->limit($limit + 1)->get();

            if ($items->count() <= $limit) {
                return new CoreCursorPaginator($items);
            }

            $items->pop();

            return new CoreCursorPaginator($items, array_map(function ($column) use ($items) {
                return $items->last()->{$column};
            }, array_keys($columns)));
        });
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
