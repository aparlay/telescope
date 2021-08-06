<?php

namespace Aparlay\Core\Api\V1\Providers;

use Aparlay\Core\Api\V1\Models\Block;
use Aparlay\Core\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        'Aparlay\Core\Api\V1\Models\Media' => 'Aparlay\Core\Api\V1\Policies\MediaPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('interact', function (User $user, $creatorId) {
            return ! Block::select(['user._id'])->user($user->_id)->creator($creatorId)->exists();
        });
    }
}
