<?php

namespace Aparlay\Core\Jobs;

use Aparlay\Core\Api\V1\Services\MediaService;
use Aparlay\Core\Models\Media;
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

class MediaWatched implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

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
    public function __construct(public array $mediaIds, public int $duration = 60, public string|null $userId = null)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $mediaService = app()->make(MediaService::class);
        $this->userId = ! empty($this->userId) ? new ObjectId($this->userId) : null;
        foreach (Media::query()->whereIn('_id', $this->mediaIds)->get() as $media) {
            $mediaService->watched($media, $this->duration, $this->userId);
        }
    }

    public function failed(Throwable $exception): void
    {
        if (($user = User::admin()->first()) !== null) {
            $user->notify(new JobFailed(self::class, $this->attempts(), $exception->getMessage()));
        }
    }
}
