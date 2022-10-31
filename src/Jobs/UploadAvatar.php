<?php

namespace Aparlay\Core\Jobs;

use Aparlay\Core\Helpers\Cdn;
use Aparlay\Core\Models\User;
use Aparlay\Core\Notifications\JobFailed;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Filesystem\FileExistsException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Throwable;

class UploadAvatar implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public User $user;

    public string $file;
    public string $user_id;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 1;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     */
    public int $maxExceptions = 1;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int|array
     */
    public $backoff = 1;

    /**
     * Create a new job instance.
     *
     * @return void
     *
     * @throws Exception
     */
    public function __construct(string $userId, string $file)
    {
        $this->onQueue(config('app.server_specific_queue'));
        $this->file = $file;
        $this->user_id = $userId;
        if (($this->user = User::user($userId)->first()) === null) {
            throw new Exception(__CLASS__.PHP_EOL.'User not found!');
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     *
     * @throws FileExistsException
     * @throws FileNotFoundException
     * @throws Exception
     */
    public function handle()
    {
        $local = Storage::disk('public');
        $b2 = Storage::disk('b2-avatars');
        $gc = Storage::disk('gc-avatars');
        $filename = basename($this->file);
        $b2->writeStream($filename, $local->readStream($this->file));
        $gc->writeStream($filename, $local->readStream($this->file));

        $this->user->avatar = Cdn::avatar($filename);
        if ($this->user->save() && $b2->fileExists($filename) && $gc->fileExists($filename)) {
            DeleteAvatar::dispatch($this->file)->delay(300)->onQueue('low');
        }
    }

    /**
     * @param  Throwable  $exception
     */
    public function failed(Throwable $exception): void
    {
        if (($user = User::admin()->first()) !== null) {
            $user->notify(new JobFailed(self::class, $this->attempts(), $exception->getMessage()));
        }
    }
}
