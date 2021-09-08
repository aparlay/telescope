<?php

namespace Aparlay\Core\Jobs;

use Aparlay\Core\Models\Media;
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
     */
    public function __construct(string $mediaId, string $file)
    {
        if (($this->media = Media::media($mediaId)->first()) === null) {
            throw new Exception(__CLASS__.PHP_EOL.'Media not found with id '.$userId);
        }

        $this->file = $file;
        $this->media_id = $mediaId;

        $this->handle();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $b2 = Storage::disk('b2-videos');
        $storage = Storage::disk('upload');
        try {
            $b2File = $this->file;

            if ($b2->exists($this->file)) {
                if (! $storage->fileExists($b2File)) {
                    $b2->writeStream($b2File, $storage->readStream($b2File));
                }
                ProcessMedia::dispatch([
                    'file' => $media->file,
                    'media_id' => (string) $media->_id,
                ])->onQueue('lowpriority');

                return;
            }

            if (($media = Media::find($this->media_id)) !== null && $storage->fileExists($media->file)) {
                ProcessMedia::dispatch([
                    'file' => $media->file,
                    'media_id' => (string) $media->_id,
                ])->onQueue('lowpriority');

                return;
            }
            
            throw new Exception(__CLASS__ . PHP_EOL . 'Nighter video file nor media object found!');
        } catch (FileOpenException $e) {
            return $e->getMessage();
        }
    }

    public function failed(Throwable $exception): void
    {
        $this->user->notify(new JobFailed(self::class, $this->attempts(), $exception->getMessage()));
    }
}
