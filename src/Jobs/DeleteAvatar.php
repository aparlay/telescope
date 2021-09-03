<?php

namespace Aparlay\Core\Jobs;

use Aparlay\Core\Models\User;
use Aparlay\Core\Notifications\JobFailed;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Throwable;

class DeleteAvatar implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public User $user;
    public array $disks = [];

    public string $file;

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
     */
    public function __construct(string $userId, string $file, array $disks = [])
    {
        $this->file = $file;
        $this->disks = ! empty($disks) ? $disks : ['public', 'b2-avatars', 'gc-avatars'];
        if (($this->user = User::user($userId)->first()) === null) {
            throw new InvalidArgumentException(__CLASS__.PHP_EOL.'User not found!');
        }
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->disks as $disk) {
            if (Storage::disk($disk)->exists($this->file)) {
                Storage::disk($disk)->delete($this->file);
            }
        }
    }

    public function failed(Throwable $exception): void
    {
        $this->user->notify(new JobFailed(self::class, $this->attempts(), $exception->getMessage()));
    }
}
