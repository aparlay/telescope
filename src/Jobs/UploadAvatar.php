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
        $this->file = $file;
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
        $resource = Storage::disk('public')->readStream($this->file);
        $filename = basename($this->file);
        Storage::disk('b2-avatars')->writeStream($filename, $resource);
        Storage::disk('gc-avatars')->writeStream($filename, $resource);

        $this->user->avatar = Cdn::avatar($filename);
        if ($this->user->save()) {
            $deleteUploaded = new DeleteAvatar((string) $this->user->_id, $this->file, ['public']);
            dispatch($deleteUploaded->delay(100)->onQueue('low'));
        }
    }

    public function failed(Throwable $exception)
    {
        $this->user->notify(new JobFailed(self::class, $this->attempts(), $exception->getMessage()));
    }
}
