<?php

namespace Aparlay\Core\Jobs;

use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\MediaComment;
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

class DeleteMediaComments implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    public string $mediaId;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries         = 10;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     */
    public int $maxExceptions = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int|array
     */
    public $backoff           = [60, 300, 1800, 3600];

    /**
     * Create a new job instance.
     *
     * @throws Exception
     *
     * @return void
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
        MediaComment::query()->media($this->mediaId)->delete();
        if (($media = Media::media($this->mediaId)->first()) !== null) {
            $user                          = $media->userObj;

            $commentCount                  = MediaComment::query()->user($media->creator['_id'])->count();
            $user->comment_count           = $commentCount;
            $user->removeFromSet('comments', [
                '_id' => new ObjectId($media->creator['_id']),
                'username' => $media->creator['username'],
                'avatar' => $media->creator['avatar'],
            ]);
            $user->count_fields_updated_at = array_merge(
                $user->count_fields_updated_at,
                ['comments' => DT::utcNow()]
            );
            $user->save();
        }
    }

    public function failed(Throwable $exception): void
    {
        if (($user = User::admin()->first()) !== null) {
            $user->notify(new JobFailed(self::class, $this->attempts(), $exception->getMessage()));
        }
    }
}
