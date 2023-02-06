<?php

namespace Aparlay\Core\Jobs;

use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\User;
use Aparlay\Core\Notifications\JobFailed;
use Exception;
use Flow\FileOpenException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ReprocessMedia implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public Media $media;
    public string $file;
    public string $media_id;

    /**
     * Create a new job instance.
     *
     * @return void
     * @throws Exception
     */
    public function __construct(string $mediaId, string $file)
    {
        if (($this->media = Media::media($mediaId)->first()) === null) {
            throw new Exception(__CLASS__.PHP_EOL.'Media not found with id '.$mediaId);
        }

        $this->file = $file;
        $this->media_id = $mediaId;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Illuminate\Contracts\Filesystem\FileExistsException
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        $b2 = Storage::disk('b2-videos');
        $mediaServer = Storage::disk('media-ftp');
        $storage = Storage::disk('upload');

        try {
            $b2File = $this->file;
            if ($b2->fileExists($this->file)) {
                if (! $mediaServer->exists($b2File)) {
                    $mediaServer->writeStream($b2File, $b2->readStream($this->file));
                }

                ProcessMedia::dispatch($this->media_id, $b2File)->onQueue('low');

                return;
            }

            if (($media = Media::find($this->media_id)) !== null && $storage->exists($media->file)) {
                UploadMedia::dispatch($media->creator['_id'], $media->_id, request()->input('file'));

                return;
            }

            throw new Exception(__CLASS__.PHP_EOL.'Nighter video file nor media object found!');
        } catch (FileOpenException $e) {
            throw new Exception(__CLASS__.PHP_EOL.$e->getMessage());
        }
    }

    public function failed(Throwable $exception): void
    {
        if (($user = User::admin()->first()) !== null) {
            $user->notify(new JobFailed(self::class, $this->attempts(), $exception->getMessage()));
        }
    }
}
