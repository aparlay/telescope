<?php

namespace Aparlay\Core\Jobs;

use Aparlay\Core\Helpers\Cdn;
use Aparlay\Core\Models\Block;
use Aparlay\Core\Models\Follow;
use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\MediaLike;
use Aparlay\Core\Models\User;
use Aparlay\Core\Notifications\JobFailed;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use MongoDB\BSON\ObjectId;
use Throwable;

class UpdateAvatar implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public User $user;
    public string $avatar;
    public string $userId;

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
    public $backoff = 30;

    /**
     * Create a new job instance.
     *
     * @return void
     *
     * @throws Exception
     */
    public function __construct(string $userId)
    {
        $this->onQueue('low');
        $this->userId = $userId;
        User::findOrFail(new ObjectId($userId));
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function handle()
    {
        $user = User::findOrFail(new ObjectId($this->userId));
        $avatar = $user->avatar;
        Media::creator($user->_id)->update(['creator.avatar' => $avatar]);
        Follow::query()->creator($user->_id)->update(['creator.avatar' => $avatar]);
        Follow::query()->user($user->_id)->update(['user.avatar' => $avatar]);
        Block::query()->creator($user->_id)->update(['creator.avatar' => $avatar]);
        Block::query()->user($user->_id)->update(['user.avatar' => $avatar]);
        MediaLike::query()->creator($user->_id)->update(['creator.avatar' => $avatar]);

        $cacheKey = 'SimpleUserCast:'.$user->_id;
        $userArray = [
            '_id' => (string) $user->_id,
            'username' => $user->username,
            'avatar' => $user->avatar ?? Cdn::avatar('default.jpg'),
            'is_verified' => $user->is_verified,
        ];

        Cache::store('octane')->put($cacheKey, json_encode($userArray), 300);
        Redis::set($cacheKey, $userArray, config('app.cache.veryLongDuration'));
    }

    public function failed(Throwable $exception)
    {
        if (($user = User::admin()->first()) !== null) {
            $user->notify(new JobFailed(self::class, $this->attempts(), $exception->getMessage()));
        }
    }
}
