<?php

namespace Aparlay\Core\Providers;

use Aparlay\Core\Api\V1\Models\Block;
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
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('like-media', function ($user, $media) {
            $isBlocked = Block::select(['user._id'])->user($user->_id)->creator($media->created_by)->first();
            if (!empty($isBlocked)) {
                return false;
            }

            return true;
        });

        Gate::define('report-media', function ($user, $media) {
            $isBlocked = Block::select(['user._id'])->user($user->_id)->creator($media->created_by)->first();
            if (!empty($isBlocked)) {
                return false;
            }

            return true;
        });

        Gate::define('report-user', function ($user, $report) {
            $isBlocked = Block::select(['user._id'])->user($user->_id)->creator($report->_id)->first();
            if (!empty($isBlocked)) {
                return false;
            }

            return true;
        });
    }
}
