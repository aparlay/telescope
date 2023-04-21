<?php

namespace Aparlay\Core\Jobs;

use Aparlay\Core\Models\Enums\MediaStatus;
use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\User;
use Aparlay\Core\Notifications\JobFailed;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use InvalidArgumentException;
use Throwable;

class UpdateUserMediaStatus implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries         = 30;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     */
    public int $maxExceptions = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int|array
     */
    public $backoff           = 300;

    /**
     * Create a new job instance.
     *
     * @throws Exception
     *
     * @return void
     */
    public function __construct(public string $userId, public int $status)
    {
        $this->onQueue('low');

        if (!in_array($this->status, MediaStatus::getAllValues())) {
            throw new InvalidArgumentException(_('Status is not supported'));
        }
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach (Media::creator($this->userId)->lazy() as $media) {
            $media->status = $this->status;
            $media->save();
        }
    }

    public function failed(Throwable $exception): void
    {
        if (($user = User::admin()->first()) !== null) {
            $user->notify(new JobFailed(self::class, $this->attempts(), $exception->getMessage()));
        }
    }
}
