<?php

namespace Aparlay\Core\Providers;

use Aparlay\Core\Api\V1\Models\Block;
use Aparlay\Core\Models\Model;
use Aparlay\Core\Observers\BlockObserver;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Model::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Block::observe(BlockObserver::class);
    }
}
