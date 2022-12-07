<?php

namespace Aparlay\Core\Jobs;

use Aparlay\Core\Helpers\Cdn;
use Aparlay\Core\Models\User;
use Aparlay\Core\Notifications\JobFailed;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Throwable;

class WarmupSimpleUserCacheJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 30;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     */
    public int $maxExceptions = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int|array
     */
    public $backoff = 10;

    /**
     * Create a new job instance.
     *
     * @return void
     * @throws Exception
     */
    public function __construct()
    {
        $this->onQueue('low');
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        User::chunk(500, function ($users) {
            $data = [];
            foreach ($users as $user) {
                $data['SimpleUserCast:'.$user->_id] = [
                    '_id' => (string) $user->_id,
                    'username' => $user->username,
                    'avatar' => $user->avatar ?? Cdn::avatar('default.jpg'),
                    'is_verified' => $user->is_verified,
                ];
            }

            Cache::store('octane')->deleteMultiple(array_keys($data));
            Cache::store('redis')->deleteMultiple(array_keys($data));

            Cache::store('octane')->setMultiple($data, config('app.cache.tenMinutes'));
            Cache::store('redis')->setMultiple($data, config('app.cache.veryLongDuration'));
        });
    }

    public function failed(Throwable $exception): void
    {
        if (($user = User::admin()->first()) !== null) {
            $user->notify(new JobFailed(self::class, $this->attempts(), $exception->getMessage()));
        }
    }
}
