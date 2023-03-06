<?php

namespace Aparlay\Core\Jobs;

use Aparlay\Core\Models\Enums\MediaStatus;
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
    public string $user_id;
    public string $media_id;
    public string $file;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 10;

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
        $this->onQueue(config('app.server_specific_queue'));
        if (($this->user = User::user($userId)->first()) === null) {
            throw new Exception(__CLASS__.PHP_EOL.'User not found with id '.$userId);
        }

        if (($this->media = Media::media($mediaId)->first()) === null) {
            throw new Exception(__CLASS__.PHP_EOL.'Media not found with id '.$userId);
        }

        $this->user_id = (string) $userId;
        $this->media_id = (string) $mediaId;
        $this->file = $file;

        if (! Storage::disk('upload')->exists($this->file) && ! config('app.is_testing')) {
            throw new Exception(__CLASS__.PHP_EOL.'File not exists.');
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
        if (($media = Media::media($this->media_id)->first()) === null) {
            throw new Exception(__CLASS__.PHP_EOL.'Media not found!');
        }
        $storage = Storage::disk('upload');
        if (! $storage->exists($this->file)) {
            throw new Exception(__CLASS__.PHP_EOL.'File not exists '.$this->file);
        }

        $newFilename = md5($this->user_id.md5_file($storage->path($this->file))).'.'.pathinfo($this->file, PATHINFO_EXTENSION);
        $filePath = $storage->path($this->file);
        $media->hash = sha1_file($filePath);
        $media->size = $storage->size($this->file);
        $media->mime_type = $storage->mimeType($this->file);
        $media->file = $newFilename;
        $media->status = MediaStatus::UPLOADED->value;

        $mediaServer = Storage::disk('media-ftp');
        if (! $mediaServer->exists($newFilename)) {
            $mediaServer->writeStream($newFilename, $storage->readStream($this->file));
        }
        $mediaServer->setVisibility($newFilename, Filesystem::VISIBILITY_PUBLIC);

        ProcessMedia::dispatch($this->media_id, $newFilename);

        BackblazeVideoUploader::dispatch($this->user_id, $this->media_id, $this->file);

        $media->save();
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
