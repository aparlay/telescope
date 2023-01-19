<?php

namespace Aparlay\Core\Jobs;

use Aparlay\Core\Models\Block;
use Aparlay\Core\Models\Follow;
use Aparlay\Core\Models\MediaLike;
use Aparlay\Core\Models\User;
use Aparlay\Core\Notifications\JobFailed;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use MongoDB\BSON\ObjectId;
use Throwable;

class DeleteUserLikes implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public string $userId;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 20;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     */
    public int $maxExceptions = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int|array
     */
    public $backoff = 60;

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
     */
    public function handle()
    {
        MediaLike::query()->user($this->userId)->chunk(200, function($models) {
            foreach ($models as $model) {
                $model->delete();
            }
        });
    }

    public function failed(Throwable $exception)
    {
        if (($user = User::admin()->first()) !== null) {
            $user->notify(new JobFailed(self::class, $this->attempts(), $exception->getMessage()));
        }
    }
}
