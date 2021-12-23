<?php

namespace Aparlay\Core\Jobs;

use Aparlay\Core\Constants\StorageType;
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

class UploadFileJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public User $user;

    public string $file;
    public string $user_id;
    public $disk;

    private $model;

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
    public function __construct(
        $model,
        string $file,
        $disk,
        $storages = []
    ) {
        $this->onQueue('low');
        $this->file = $file;
        $this->user_id = $model->user_id;
        $this->user = User::user($this->user_id)->firstOrFail();
        $this->model = $model;
        $this->disk = $disk;
        $this->storages = $storages;
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
        $filename = basename($this->file);

        foreach ($this->storages as $storageName) {
            $storage = Storage::disk($storageName);
            $storage->writeStream($filename, Storage::disk($this->disk)->readStream($this->file));
        }

//        $this->model->file = Cdn::document($filename);
//
//        if ($this->user->save() && $b2->exists($filename) && $gc->exists($filename)) {
//            //$deleteUploaded = new DeleteAvatar($this->file);
//            //dispatch($deleteUploaded->delay(300)->onQueue('low'));
//        }
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
