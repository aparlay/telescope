<?php

namespace Aparlay\Core\Jobs;

use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\User;
use Aparlay\Core\Notifications\JobFailed;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Filesystem\FileExistsException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use MongoDB\BSON\ObjectId;
use Throwable;

class UploadMedia implements ShouldQueue
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
    public int $maxExceptions = 1;

    /**
     * Create a new job instance.
     *
     * @return void
     *
     * @throws Exception
     */
    public function __construct(string|ObjectId $userId, string|ObjectId $mediaId, string $file)
    {
        if (($this->user = User::user($userId)->first()) === null) {
            throw new Exception(__CLASS__.PHP_EOL.'User not found with id '.$userId);
        }

        if (($this->media = Media::media($mediaId)->first()) === null) {
            throw new Exception(__CLASS__.PHP_EOL.'Media not found with id '.$userId);
        }

        $this->file = $file;
        if (! Storage::disk('upload')->exists($this->file)) {
            throw new Exception(__CLASS__.PHP_EOL.'File not exists '.$this->file);
        }
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
        $mediaServer = Storage::disk('media-ftp');

        $newFilename = md5(time());
        $hash = '';
        $mime = '';
        $size = 0;
        if ($storage->exists($this->file)) {
            $newFilename = md5_file($storage->path($this->file)).'.'.pathinfo($this->file, PATHINFO_EXTENSION);
            $filePath = $storage->path($this->file);
            $hash = sha1_file($filePath);
            $size = $storage->size($this->file);
            $mime = $storage->mimeType($this->file);
            $mediaServer->writeStream($newFilename, $storage->readStream($this->file));
            $mediaServer->setVisibility($newFilename, Filesystem::VISIBILITY_PUBLIC);
            ProcessMedia::dispatch($this->user->_id, $this->media->_id, $newFilename)->onQueue('lowpriority');
            BackblazeVideoUploader::dispatch($this->user->_id, $this->media->_id, $this->file)->onQueue('lowpriority');
        } else {
            throw new Exception(__CLASS__.PHP_EOL.'File not exists '.$this->file);
        }

        $this->media->mime_type = $mime;
        $this->media->hash = $hash;
        $this->media->size = $size;
        $this->media->file = $newFilename;
        $this->media->status = Media::STATUS_UPLOADED;
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
        $this->user->notify(new JobFailed(self::class, $this->attempts(), $exception->getMessage()));
    }
}
