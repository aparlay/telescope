<?php

namespace Aparlay\Core\Jobs;

use Aparlay\Core\Helpers\DT;
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
use Illuminate\Support\Facades\Redis;
use MongoDB\BSON\ObjectId;
use Throwable;

class DeleteMediaLikes implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public string $mediaId;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 10;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     */
    public int $maxExceptions = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int|array
     */
    public $backoff = [60, 300, 1800, 3600];

    /**
     * Create a new job instance.
     *
     * @return void
     *
     * @throws Exception
     */
    public function __construct(string $mediaId)
    {
        $this->onQueue('low');
        $this->mediaId = $mediaId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        MediaLike::media($this->mediaId)->delete();
        if (($media = Media::find(new ObjectId($this->mediaId))) !== null) {
            $user = $media->userObj;

            $likeCount = MediaLike::user($media->creator['_id'])->count();
            $user->like_count = $likeCount;
            $user->removeFromSet('likes', [
                '_id' => new ObjectId($media->creator['_id']),
                'username' => $media->creator['username'],
                'avatar' => $media->creator['avatar'],
            ]);
            $user->count_fields_updated_at = array_merge(
                $user->count_fields_updated_at,
                ['likes' => DT::utcNow()]
            );
            $user->save();

            // Reset the Redis cache
            MediaLike::cacheByUserId((string) $media->creator['_id'], true);
        }
    }

    public function failed(Throwable $exception): void
    {
        if (($user = User::admin()->first()) !== null) {
            $user->notify(new JobFailed(self::class, $this->attempts(), $exception->getMessage()));
        }
    }
}
