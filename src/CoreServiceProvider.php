<?php

namespace Aparlay\Core;

use Aparlay\Core\Admin\Components\DatePicker;
use Aparlay\Core\Admin\Components\MediaCover;
use Aparlay\Core\Admin\Components\Modals;
use Aparlay\Core\Admin\Components\SortableColumnHeader;
use Aparlay\Core\Admin\Components\UserNameAvatar;
use Aparlay\Core\Admin\Components\UtcFormatter;
use Aparlay\Core\Admin\Components\WireDropDownList;
use Aparlay\Core\Admin\Livewire\Components\DateRangePicker;
use Aparlay\Core\Admin\Livewire\Components\UserModerationButton;
use Aparlay\Core\Admin\Livewire\EmailsTable;
use Aparlay\Core\Admin\Livewire\GlobalSearch;
use Aparlay\Core\Admin\Livewire\MediasModerationTable;
use Aparlay\Core\Admin\Livewire\MediasTable;
use Aparlay\Core\Admin\Livewire\Modals\UserNoteCreateModal;
use Aparlay\Core\Admin\Livewire\Modals\UserNoteDeleteModal;
use Aparlay\Core\Admin\Livewire\Modals\UserVerificationModal;
use Aparlay\Core\Admin\Livewire\NotesTable;
use Aparlay\Core\Admin\Livewire\SettingsTable;
use Aparlay\Core\Admin\Livewire\UsersModerationTable;
use Aparlay\Core\Admin\Livewire\UsersTable;
use Aparlay\Core\Commands\AnalyticsDailyCommand;
use Aparlay\Core\Commands\AnalyticsTwoMonthCommand;
use Aparlay\Core\Commands\CleanupCommand;
use Aparlay\Core\Commands\CoreCommand;
use Aparlay\Core\Commands\DbBackupCommand;
use Aparlay\Core\Commands\DbRestoreCommand;
use Aparlay\Core\Commands\HashtagScoreCommand;
use Aparlay\Core\Commands\MeilisearchSettingCommand;
use Aparlay\Core\Commands\ResetMediaCountersCommand;
use Aparlay\Core\Commands\ResetUserCountersCommand;
use Aparlay\Core\Commands\RoleCommand;
use Aparlay\Core\Commands\UserScoreCommand;
use Aparlay\Core\Commands\UserScoreDailyCommand;
use Aparlay\Core\Commands\VideoReprocessCommand;
use Aparlay\Core\Commands\VideoScoreCommand;
use Aparlay\Core\Commands\VideoScoreDailyCommand;
use Aparlay\Core\Commands\VideoScoreHourlyCommand;
use Aparlay\Core\Commands\VideoUpdateInfoCommand;
use Aparlay\Core\Commands\WarmupCacheCommand;
use Aparlay\Core\Commands\WsCommand;
use Aparlay\Core\Helpers\ConfigHelper;
use Aparlay\Core\Helpers\IP;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

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
        }
        $this->app->register(\Aparlay\Core\Api\V1\Providers\AuthServiceProvider::class);
        $this->app->register(\Aparlay\Core\Api\V1\Providers\EventServiceProvider::class);
        $this->app->register(\Aparlay\Core\Admin\Providers\AdminServiceProvider::class);
        $this->app->register(\Aparlay\Core\Admin\Providers\AuthServiceProvider::class);
        $this->app->register(\Aparlay\Core\Admin\Providers\EventServiceProvider::class);

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
                __DIR__.'/../resources/admin' => public_path('admin'),
            ], 'public');

            $this->commands([
                CoreCommand::class,
                RoleCommand::class,
                VideoReprocessCommand::class,
                VideoScoreCommand::class,
                VideoScoreDailyCommand::class,
                VideoScoreHourlyCommand::class,
                VideoUpdateInfoCommand::class,
                UserScoreCommand::class,
                UserScoreDailyCommand::class,
                HashtagScoreCommand::class,
                MeilisearchSettingCommand::class,
                WsCommand::class,
                WarmupCacheCommand::class,
                AnalyticsTwoMonthCommand::class,
                AnalyticsDailyCommand::class,
                CleanupCommand::class,
                DbRestoreCommand::class,
                DbBackupCommand::class,
                ResetMediaCountersCommand::class,
                ResetUserCountersCommand::class,
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

        $this->registerLivewireComponents();

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

    public function registerLivewireComponents()
    {
        $components = [
            'users-table' => UsersTable::class,
            'users-moderation-table' => UsersModerationTable::class,
            'modals.user-verification-modal' => UserVerificationModal::class,
            'date-picker' => DatePicker::class,
            'user-moderation-button' => UserModerationButton::class,
            'medias-table' => MediasTable::class,
            'medias-moderation-table' => MediasModerationTable::class,
            'settings-table' => SettingsTable::class,
            'emails-table' => EmailsTable::class,
            'notes-table' => NotesTable::class,
            'modals' => Modals::class,
            'modals.user-notes-delete-modal' => UserNoteDeleteModal::class,
            'modals.user-notes-create-modal' => UserNoteCreateModal::class,
            'components.date-range-picker' => DateRangePicker::class,
            'global-search' => GlobalSearch::class,
        ];

        foreach ($components as $name => $class) {
            Livewire::component($name, $class);
        }

        Blade::component('date-picker', DatePicker::class);
        Blade::component('sortable-column-header', SortableColumnHeader::class);
        Blade::component('wire-dropdown-list', WireDropDownList::class);
        Blade::component('username-avatar', UserNameAvatar::class);
        Blade::component('media-cover', MediaCover::class);
        Blade::component('utc-formatter', UtcFormatter::class);
    }
}
