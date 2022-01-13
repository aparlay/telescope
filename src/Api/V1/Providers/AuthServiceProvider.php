<?php

namespace Aparlay\Core\Api\V1\Providers;

use Aparlay\Core\Api\V1\Models\Block;
use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Models\MediaLike;
use Aparlay\Core\Api\V1\Models\UserDocument;
use Aparlay\Core\Api\V1\Policies\MediaLikePolicy;
use Aparlay\Core\Api\V1\Policies\MediaPolicy;
use Aparlay\Core\Api\V1\Policies\UserDocumentPolicy;
use Aparlay\Core\Api\V1\Policies\UserPolicy;
use Aparlay\Core\Api\V1\Policies\WalletPolicy;
use Aparlay\Core\Models\User;
use Aparlay\Payout\Api\V1\Models\Wallet;
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
        Media::class => MediaPolicy::class,
        User::class => UserPolicy::class,
        MediaLike::class => MediaLikePolicy::class,
        UserDocument::class => UserDocumentPolicy::class,
        Wallet::class => WalletPolicy::class,
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
