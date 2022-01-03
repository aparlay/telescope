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
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * This job could be used to upload files to any storage.
 */
class UploadFileJob extends AbstractJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private string $fileName;
    private string $fileDisk;
    private Collection $storages;
    private $storageFilePath;


    /**
     * Create a new job instance.
     *
     * @return void
     *
     * @throws Exception
     */
    public function __construct(
        string $fileName,
        string $fileDisk,
        Collection $storages,
        $storageFilePath = null
    ) {
        parent::__construct();

        $this->fileDisk = $fileDisk;
        $this->fileName = $fileName;
        $this->storages = $storages;
        $this->storageFilePath = $storageFilePath;
    }

    public function handle()
    {
        try {
            $storageFilePath = $this->storageFilePath ?? basename($this->fileName);

            $this->storages->each(function ($storageName) use ($storageFilePath) {
                $storage = Storage::disk($storageName);

                Log::debug('storage file path '.$storageFilePath);

                $storage->writeStream($storageFilePath, Storage::disk($this->fileDisk)->readStream($this->fileName));
                if (! $storage->exists($storageFilePath)) {
                    throw new \Error("{$storageFilePath} failed to upload to {$storageName}");
                }
            });
        } catch (Throwable $throwable) {
            Log::error('Unable to save file: '.$throwable->getMessage());
        }
    }

    /**
     * @param Throwable $exception
     */
    public function failed(Throwable $exception): void
    {
        if (($user = User::admin()->first()) !== null) {
            $user->notify(new JobFailed(self::class, $this->attempts(), $exception->getMessage()));
        }
    }
}
