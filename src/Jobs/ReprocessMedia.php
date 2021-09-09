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
        $storage = Storage::disk('public');
        
        try {
            $b2File = $this->file;
            ProcessMedia::dispatch($this->media_id, $b2File)->onQueue('lowpriority');
            return;
            if ($b2->exists($this->file)) {
                if (! $storage->exists($b2File)) {
                    $b2->writeStream($b2File, $storage->readStream('upload/' . $b2File));
                }

                ProcessMedia::dispatch($this->media_id, $b2File)->onQueue('lowpriority');

                return;
            }

            if (($media = Media::find($this->media_id)) !== null && $storage->exists('upload/' . $media->file)) {

                UploadMedia::dispatch($media->created_by, $media->_id, $media->file)->onQueue('lowpriority');

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
