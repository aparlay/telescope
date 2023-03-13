<?php

namespace Aparlay\Core\Jobs;

use Aparlay\Core\Constants\StorageType;
use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\User;
use Aparlay\Core\Notifications\JobFailed;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;
use Spatie\Image\Image;
use Throwable;

/**
 * This job could be used to upload files to any storage.
 */
class BlurImageJob extends AbstractJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 30;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     */
    public int $maxExceptions = 10;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int|array
     */
    public $backoff = [3, 10, 15, 30, 60];

    /**
     * Create a new job instance.
     *
     * @return void
     *
     * @throws Exception
     */
    public function __construct(private string $mediaId)
    {
        parent::__construct();
    }

    public function handle()
    {
        if (($media = Media::media($this->mediaId)->first()) === null) {
            Log::error(__CLASS__.PHP_EOL.'Media not found!');

            return;
        }

        try {
            $storage = Storage::disk('upload');
            $image = $media->filename.'.jpg';

            if (Storage::disk(StorageType::GC_COVERS)->fileMissing($image)) {
                Log::error("Failed find to {$image} in ".StorageType::GC_COVERS.' storage');

                return;
            }

            if ($storage->fileMissing($image)) {
                $storage->writeStream($image, Storage::disk(StorageType::GC_COVERS)->readStream($image));
            } else {
                Log::debug('image to blur file already exists '.$image);
            }

            $blurredImage = Uuid::uuid4().'.jpg';
            Image::load($storage->path($image))->blur(15)->quality(70)->save($storage->path($blurredImage));
            UploadFileJob::dispatch(
                $image,
                'upload',
                collect([StorageType::GC_COVERS]),
                $blurredImage
            );
            $media->image_blurred = $blurredImage;
            $media->saveQuietly();

            //$storage->delete($blurredImage);
            //$storage->delete($image);
        } catch (Throwable $throwable) {
            Log::error('Unable to save file: '.$throwable->getMessage());
        }
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