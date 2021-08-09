<?php

namespace Aparlay\Core\Jobs;

use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Models\Media;
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

class BackblazeVideoUploader implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public User $user;
    public Media $media;
    public string $file;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 30;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     */
    public int $maxExceptions = 3;

    /**
     * Create a new job instance.
     *
     * @return void
     *
     * @throws Exception
     */
    public function __construct(string $userId, string $mediaId, string $file)
    {
        if (($this->user = User::user($userId)->first()) === null) {
            throw new Exception(__CLASS__ . PHP_EOL . 'User not found with id ' . $userId);
        }

        if (($this->media = Media::media($mediaId)->first()) === null) {
            throw new Exception(__CLASS__ . PHP_EOL . 'Media not found with id ' . $userId);
        }

        $this->file = $file;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws FileExistsException
     * @throws FileNotFoundException
     */
    public function handle()
    {
        $storage = Storage::disk('upload');
        $b2 = Storage::disk('b2-videos');

        $newFilename = md5_file($storage->path($this->file)).'.'.pathinfo($this->file, PATHINFO_EXTENSION);
        $fileHistory = [
            'mime_type' => '',
            'hash' => '',
            'size' => 0,
            'file' => $newFilename,
            'created_at' => DT::utcNow(),
        ];
        if ($storage->exists($this->file)) {
            $filePath = $storage->path($this->file);
            $fileHistory['hash'] = sha1_file($filePath);
            $fileHistory['size'] = $storage->size($this->file);
            $fileHistory['mime_type'] = $storage->mimeType($this->file);
            $b2->writeStream($newFilename, $storage->readStream($this->file));
            $storage->delete($this->file);
        } else {
            $this->user->notify(new JobFailed(self::class, $this->attempts(), 'File not exists'));
        }

        if (($this->file !== $newFilename) && $b2->exists($this->file)) {
            $b2->move($this->file, $newFilename);
        }

        $this->media->addToSet('files_history', $fileHistory);
        $this->media->save();
    }

    public function failed(Throwable $exception): void
    {
        $this->user->notify(new JobFailed(self::class, $this->attempts(), $exception->getMessage()));
    }

    /**
     * Calculate the number of seconds to wait before retrying the job.
     *
     * @return int
     */
    public function backoff()
    {
        return $this->attempts() * 60;
    }
}