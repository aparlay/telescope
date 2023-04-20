<?php

namespace Aparlay\Core\Jobs;

use Aparlay\Core\Events\MediaProcessingCompletedEvent;
use Aparlay\Core\Microservices\ffmpeg\MediaClient;
use Aparlay\Core\Microservices\ffmpeg\OptimizeRequest;
use Aparlay\Core\Microservices\ffmpeg\OptimizeResponse;
use Aparlay\Core\Microservices\ffmpeg\RemoveRequest;
use Aparlay\Core\Microservices\ffmpeg\UploadRequest;
use Aparlay\Core\Models\Enums\MediaStatus;
use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\User;
use Aparlay\Core\Notifications\JobFailed;
use Aparlay\Core\Notifications\VideoPending;
use Exception;
use Grpc\ChannelCredentials;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use MongoDB\BSON\ObjectId;
use Throwable;

class ProcessMedia implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    public Media|null $media;
    public string $file;
    public string $media_id;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries         = 10;

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
    public function __construct(string|ObjectId $mediaId, string|ObjectId $file)
    {
        $this->onQueue(config('app.server_specific_queue'));
        if (($this->media = Media::media($mediaId)->first()) === null) {
            throw new Exception(__CLASS__ . PHP_EOL . 'Media not found with id ' . $mediaId);
        }

        $this->file     = $file;
        $this->media_id = $mediaId;
    }

    public function middleware()
    {
        return [new WithoutOverlapping($this->media_id)];
    }

    /**
     * Execute the job.
     *
     * @throws Exception
     *
     * @return void
     */
    public function handle()
    {
        Redis::funnel(__CLASS__)->releaseAfter(300)->limit(1)
            ->then(function () {
                $this->processVideo();
            }, function () {
                $this->release(60);
            });
    }

    public function failed(Throwable $exception): void
    {
        if (($user = User::admin()->first()) !== null) {
            $user->notify(new JobFailed(self::class, $this->attempts(), $exception->getMessage()));
        }
    }

    public function processVideo()
    {
        $client               = new MediaClient(config('app.media.grpc'), [
            'credentials' => ChannelCredentials::createInsecure(),
        ]);

        if (($media = Media::media($this->media_id)->first()) === null) {
            Log::error(__CLASS__ . PHP_EOL . 'Media not found!');

            throw new Exception(__CLASS__ . PHP_EOL . 'Media not found!');
        }

        $keepStatus           = null;
        if ($media->is_completed) {
            $keepStatus = $media->status;
        }

        $withoutTouch         = [
            'status' => false,
            'length' => false,
            'processing_log' => false,
        ];

        // check quality
        $media->status        = MediaStatus::IN_PROGRESS->value;
        $optimizeReq          = new OptimizeRequest();
        $toRemoveFiles[]      = $src = config('app.media.path') . $this->file;
        $optimizeReq->setSrc($src);
        [$response, $status]  = $client->Quality($optimizeReq)->wait();

        if ($status->code !== 0) {
            $media->addToSet('processing_log', 'Error: Cannot check video quality');
            $media->save($withoutTouch);
        }

        if ($response instanceof OptimizeResponse) {
            $quality = trim($response->getResult());
            switch ($quality) {
                case 'high':
                    $media->addToSet('processing_log', '1. Quality checked: High');

                    break;
                case 'low':
                    $media->addToSet('processing_log', '1. Quality checked: Low');

                    break;
                default:
                    $media->addToSet('processing_log', '1. Quality checked: UnKnown [' . $quality . ']');
            }
            $media->save($withoutTouch);
        }

        // check audio
        [$response, $status]  = $client->LowVolume($optimizeReq)->wait();
        if ($status->code !== 0) {
            $media->addToSet('processing_log', 'Error: Cannot check low audio');
            $media->save($withoutTouch);
        }

        if ($response instanceof OptimizeResponse) {
            $volume = trim($response->getResult());
            switch ($volume) {
                case 'Mute':
                    $media->addToSet('processing_log', '2. Audio checked: Mute');

                    break;
                case 'OK':
                    $media->addToSet('processing_log', '2. Audio checked: OK');

                    break;
                default:
                    $media->addToSet('processing_log', '2. Audio checked: Low Volume (' . $volume . ')');
            }
            $media->save($withoutTouch);
        }

        [$response, $status]  = $client->Duration($optimizeReq)->wait();
        if ($status->code !== 0) {
            $media->addToSet('processing_log', 'Error: Cannot check video duration');
            $media->save($withoutTouch);
            Log::error(__CLASS__ . PHP_EOL . 'Cannot check video duration but keep moving');
        // throw new Exception(__CLASS__.PHP_EOL.'Cannot check video duration');
        } else {
            $media->length = (float) $response->GetSec();
            $media->addToSet('processing_log', '3. Duration checked: ' . $media->length . ' Sec');
            if ($media->length > 60.0) {
                $toRemoveFiles[]     = $src = config('app.media.path') . '1-trimed-' . $this->file;
                $optimizeReq->setDes($src);
                [$response, $status] = $client->Trim($optimizeReq)->wait();
                if ($status->code !== 0) {
                    $media->addToSet('processing_log', 'Error: Cannot do video trimming');
                    $media->save($withoutTouch);
                    Log::error(__CLASS__ . PHP_EOL . 'Cannot do video trim');
                }
                $media->length       = 60.0;
                $media->addToSet('processing_log', '4. Video is trimmed to 60 Sec: Ok');
                $media->save($withoutTouch);
                $optimizeReq->setSrc($src);
            }
        }

        // normalize audio
        if (isset($volume) && $volume === 'OK') {
            $toRemoveFiles[]     = $src = config('app.media.path') . '2-normalized-' . $this->file;
            $optimizeReq->setDes($src);
            [$response, $status] = $client->NormalizeAudio($optimizeReq)->wait();
            if ($status->code !== 0) {
                $media->addToSet('processing_log', 'Error: Cannot normalize audio');
                $media->save($withoutTouch);
                Log::error(__CLASS__ . PHP_EOL . 'Cannot do audio normalization');
            } else {
                $media->addToSet('processing_log', '5. Audio normalization: Ok');
                $media->save($withoutTouch);
            }
        }

        // watermark
        $optimizeReq->setSrc($src);
        $mp4ConvertedFile     = basename($this->file, '.' . pathinfo($this->file, PATHINFO_EXTENSION)) . '.mp4';
        $toRemoveFiles[]      = $src = config('app.media.path') . '3-watermarked-' . $mp4ConvertedFile;
        $optimizeReq->setDes($src);
        $optimizeReq->setUsername('@' . $media->userObj->username);
        [$response, $status]  = $client->Watermark($optimizeReq)->wait();
        if ($status->code !== 0) {
            $media->addToSet('processing_log', 'Error: Cannot do video watermarking');
            $media->save($withoutTouch);
            Log::error(__CLASS__ . PHP_EOL . 'Cannot do video watermarking');
        }
        $media->addToSet('processing_log', '6. Video Watermarking: Ok');
        $media->save($withoutTouch);

        // check quality
        $uploadReq            = new UploadRequest();
        $uploadReq->setSrc($src);
        $uploadReq->setDes('videos/' . $mp4ConvertedFile);
        [$response, $status]  = $client->UploadVideo($uploadReq)->wait();
        if ($status->code !== 0) {
            $media->addToSet('processing_log', 'Error: Cannot upload video');
            $media->save($withoutTouch);
            Log::error(__CLASS__ . PHP_EOL . 'Cannot upload video');
            Log::error(__CLASS__ . PHP_EOL . json_encode($response));

            throw new Exception(__CLASS__ . PHP_EOL . 'Cannot upload video');
        }

        $media->file          = config('app.cdn,videos') . $mp4ConvertedFile;
        $media->addToSet('processing_log', '7. Video Uploading: Ok');
        $withoutTouchWithFile = [
            'status' => false,
            'file' => false,
            'length' => false,
            'processing_log' => false,
        ];
        $media->save($withoutTouchWithFile);

        $toRemoveFiles[]      = $cover = config('app.media.path') . str_replace('.mp4', '.jpg', $mp4ConvertedFile);

        $optimizeReq->setSrc($src);
        $optimizeReq->setDes($cover);
        [$response, $status]  = $client->CreateCover($optimizeReq)->wait();
        if ($status->code !== 0) {
            $media->addToSet('processing_log', 'Error: Cannot create video cover');
            $media->save($withoutTouch);
            Log::error(__CLASS__ . PHP_EOL . 'Cannot create video cover');

            throw new Exception(__CLASS__ . PHP_EOL . 'Cannot create video cover');
        }
        $media->addToSet('processing_log', '8. Video Cover generating: Ok');
        $media->save($withoutTouch);

        $uploadReq->setSrc($cover);
        $uploadReq->setDes('covers/' . str_replace('.mp4', '.jpg', $mp4ConvertedFile));
        [$response, $status]  = $client->UploadCover($uploadReq)->wait();
        if ($status->code !== 0) {
            $media->addToSet('processing_log', 'Error: Cannot upload cover');
            $media->save($withoutTouch);
            Log::error(__CLASS__ . PHP_EOL . 'Cannot upload cover');

            throw new Exception(__CLASS__ . PHP_EOL . 'Cannot upload cover');
        }
        $media->status        = MediaStatus::COMPLETED->value;
        $media->addToSet('processing_log', '9. Video Cover uploading: Ok');
        $media->save($withoutTouch);

        $i                    = 10;
        foreach ($toRemoveFiles as $toRemoveFile) {
            $removeReq           = new RemoveRequest();
            $removeReq->setFile($toRemoveFile);
            [$response, $status] = $client->Remove($removeReq)->wait();
            if ($status->code !== 0) {
                $media->addToSet('processing_log', 'Error: Cannot remove ' . $toRemoveFile);
                $media->save($withoutTouch);
                Log::error(__CLASS__ . PHP_EOL . 'Cannot remove ' . $toRemoveFile);

                throw new Exception(__CLASS__ . PHP_EOL . 'Cannot remove ' . $toRemoveFile);
            }
            $media->addToSet('processing_log', ++$i . '. Remove files: ' . $toRemoveFile);
        }

        $media->file          = $mp4ConvertedFile;
        $media->status        = $keepStatus ?? MediaStatus::COMPLETED->value;
        $touchWithTrue        = [
            'status' => true,
            'length' => true,
            'processing_log' => true,
            'file' => true,
        ];
        $media->save($touchWithTrue);

        $media->refresh();
        $media->notify(new VideoPending());
        MediaProcessingCompletedEvent::dispatch($media);
        BunnyCdnPurgeUrlJob::dispatch((string) $media->_id);
    }
}
