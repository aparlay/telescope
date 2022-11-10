<?php

namespace Aparlay\Core\Commands;

use Aparlay\Core\Helpers\Cdn;
use Aparlay\Core\Models\User;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Isolatable;
use Illuminate\Support\Facades\Cache;

class WarmupCacheCommand extends Command implements Isolatable
{
    public $signature = 'core:warmup';

    public $description = 'This command is responsible for warm up cache';

    public function handle()
    {
        User::chunk(500, function ($users) {
            $redis = [];
            foreach ($users as $user) {
                $redis['SimpleUserCast:'.$user->_id] = [
                    '_id' => (string) $user->_id,
                    'username' => $user->username,
                    'avatar' => $user->avatar ?? Cdn::avatar('default.jpg'),
                    'is_verified' => $user->is_verified,
                ];
                $this->info('User '.$user->_id.' cached');
            }

            Cache::store('redis')->setMultiple($redis, config('app.cache.veryLongDuration'));
        });

        return self::SUCCESS;
    }
}
