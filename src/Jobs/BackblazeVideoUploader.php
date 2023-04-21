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
    public string $user_id;
    public string $media_id;
    public string $file;

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
    public $backoff           = 10;

    /**
     * Create a new job instance.
     *
     * @throws Exception
     *
     * @return void
     */
    public function __construct(string $userId, string $mediaId, string $file)
    {
        $this->onQueue(config('app.server_specific_queue'));
        if (($this->user = User::user($userId)->first()) === null) {
            throw new Exception(__CLASS__ . PHP_EOL . 'User not found with id ' . $userId);
        }

        if (($this->media = Media::media($mediaId)->first()) === null) {
            throw new Exception(__CLASS__ . PHP_EOL . 'Media not found with id ' . $userId);
        }

        $this->user_id  = (string) $userId;
        $this->media_id = (string) $mediaId;
        $this->file     = $file;
    }

    /**
     * Execute the job.
     *
     * @throws FileExistsException
     * @throws FileNotFoundException
     *
     * @return void
     */
    public function handle()
    {
        if (($this->user = User::user($this->user_id)->first()) === null) {
            throw new Exception(__CLASS__ . PHP_EOL . 'User not found with id ' . $this->user_id);
        }

        if (($this->media = Media::media($this->media_id)->first()) === null) {
            throw new Exception(__CLASS__ . PHP_EOL . 'Media not found with id ' . $this->media_id);
        }
        $storage     = Storage::disk('upload');
        $b2          = Storage::disk('b2-videos');

        $newFilename = md5_file($storage->path($this->file)) . '.' . pathinfo($this->file, PATHINFO_EXTENSION);
        $fileHistory = [
            'mime_type' => '',
            'hash' => '',
            'size' => 0,
            'file' => $newFilename,
            'created_at' => DT::utcNow(),
        ];
        if ($storage->exists($this->file)) {
            $filePath                 = $storage->path($this->file);
            $fileHistory['hash']      = sha1_file($filePath);
            $fileHistory['size']      = $storage->size($this->file);
            $fileHistory['mime_type'] = $storage->mimeType($this->file);
            if ($b2->fileMissing($newFilename)) {
                $b2->writeStream($newFilename, $storage->readStream($this->file));
            }
            $storage->delete($this->file);
        } else {
            $this->user->notify(new JobFailed(self::class, $this->attempts(), 'File not exists'));
        }

        if (($this->file !== $newFilename) && $b2->fileExists($this->file)) {
            $b2->move($this->file, $newFilename);
        }

        $this->media->addToSet('files_history', $fileHistory);
        $this->media->save();
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

    public function failed(Throwable $exception): void
    {
        if (($user = User::admin()->first()) !== null) {
            $user->notify(new JobFailed(self::class, $this->attempts(), $exception->getMessage()));
        }
    }
}
