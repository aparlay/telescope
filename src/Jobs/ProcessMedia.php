<?php

namespace Aparlay\Core\Jobs;

use Aparlay\Core\Microservices\ffmpeg\MediaClient;
use Aparlay\Core\Microservices\ffmpeg\OptimizeRequest;
use Aparlay\Core\Microservices\ffmpeg\OptimizeResponse;
use Aparlay\Core\Microservices\ffmpeg\RemoveRequest;
use Aparlay\Core\Microservices\ffmpeg\UploadRequest;
use Aparlay\Core\Microservices\ws\WsChannel;
use Aparlay\Core\Models\Media;
use Aparlay\Core\Notifications\JobFailed;
use Exception;
use Grpc\ChannelCredentials;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\VarDumper\VarDumper;
use Throwable;

class ProcessMedia implements ShouldQueue
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
            throw new Exception(__CLASS__.PHP_EOL.'Media not found with id '.$mediaId);
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
        $client = new MediaClient(config('app.media.grpc'), [
            'credentials' => ChannelCredentials::createInsecure(),
        ]);

        if (($media = Media::find($this->media_id)) === null) {
            throw new Exception(__CLASS__.PHP_EOL.'Media not found!');
        }

        $keepStatus = null;
        if ($media->is_completed) {
            $keepStatus = $media->status;
        }

        $withoutTouch = [
            'status' => false,
            'length' => false,
            'processing_log' => false,
        ];

        // check quality
        $media->status = Media::STATUS_IN_PROGRESS;
        $optimizeReq = new OptimizeRequest();
        $toRemoveFiles[] = $src = config('app.media.path').$this->file;
        $optimizeReq->setSrc($src);
        [$response, $status] = $client->Quality($optimizeReq)->wait();

        if ($status->code !== 0) {
            VarDumper::dump($status);
            $media->status = Media::STATUS_FAILED;
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
                    $media->addToSet('processing_log', '1. Quality checked: UnKnown ['.$quality.']');
            }
            $media->save($withoutTouch);
        }

        // check audio
        [$response, $status] = $client->LowVolume($optimizeReq)->wait();
        if ($status->code !== 0) {
            VarDumper::dump($status);
            $media->status = Media::STATUS_FAILED;
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
                    $media->addToSet('processing_log', '2. Audio checked: Low Volume ('.$volume.')');
            }
            $media->save($withoutTouch);
        }

        [$response, $status] = $client->Duration($optimizeReq)->wait();
        if ($status->code !== 0) {
            VarDumper::dump($status);
            $media->status = Media::STATUS_FAILED;
            $media->save($withoutTouch);
            throw new Exception(__CLASS__ . PHP_EOL . 'Cannot check video duration');
        }

        $media->length = (float) $response->GetSec();
        $media->addToSet('processing_log', '3. Duration checked: '.$media->length.' Sec');
        if ($media->length > 60.0) {
            $toRemoveFiles[] = $src = config('app.media.path').'1-trimed-'.$this->file;
            $optimizeReq->setDes($src);
            [$response, $status] = $client->Trim($optimizeReq)->wait();
            if ($status->code !== 0) {
                VarDumper::dump($status);
                $media->status = Media::STATUS_FAILED;
                $media->save($withoutTouch);
                throw new Exception(__CLASS__ . PHP_EOL . 'Cannot do video trim');
            }
            $media->length = 60.0;
            $media->addToSet('processing_log', '4. Video is trimmed to 60 Sec: Ok');
            $media->save($withoutTouch);
            $optimizeReq->setSrc($src);
        }

        // normalize audio
        if ($volume === 'OK') {
            $toRemoveFiles[] = $src = config('app.media.path').'2-normalized-'.$this->file;
            $optimizeReq->setDes($src);
            [$response, $status] = $client->NormalizeAudio($optimizeReq)->wait();
            if ($status->code !== 0) {
                $media->status = Media::STATUS_FAILED;
                $media->save($withoutTouch);
                throw new Exception(__CLASS__ . PHP_EOL . 'Cannot do audio normalization');
            }
            $media->addToSet('processing_log', '5. Audio normalization: Ok');
            $media->save($withoutTouch);
        }

        // watermark
        $optimizeReq->setSrc($src);
        $mp4ConvertedFile = basename($this->file, '.'.pathinfo($this->file, PATHINFO_EXTENSION)).'.mp4';
        $toRemoveFiles[] = $src = config('app.media.path').'3-watermarked-'.$mp4ConvertedFile;
        $optimizeReq->setDes($src);
        $optimizeReq->setUsername('@'.$media->user->username);
        [$response, $status] = $client->Watermark($optimizeReq)->wait();
        if ($status->code !== 0) {
            VarDumper::dump($status);
            $media->status = Media::STATUS_FAILED;
            $media->save($withoutTouch);
            throw new Exception(__CLASS__ . PHP_EOL . 'Cannot do video watermarking');
        }
        $media->addToSet('processing_log', '6. Video Watermarking: Ok');
        $media->save($withoutTouch);

        // check quality
        $uploadReq = new UploadRequest();
        $uploadReq->setSrc($src);
        $uploadReq->setDes('videos/'.$mp4ConvertedFile);
        [$response, $status] = $client->UploadVideo($uploadReq)->wait();
        if ($status->code !== 0) {
            VarDumper::dump($status);
            $media->status = Media::STATUS_FAILED;
            $media->save($withoutTouch);
            throw new Exception(__CLASS__ . PHP_EOL . 'Cannot do video upload');
        }

        $media->file = config('app.cdn,videos').$mp4ConvertedFile;
        $media->addToSet('processing_log', '7. Video Uploading: Ok');
        $withoutTouchWithFile = [
            'status' => false,
            'file' => false,
            'length' => false,
            'processing_log' => false,
        ];
        $media->save($withoutTouchWithFile);

        $toRemoveFiles[] = $cover = config('app.media.path').str_replace('.mp4', '.jpg', $mp4ConvertedFile);

        $optimizeReq->setSrc($src);
        $optimizeReq->setDes($cover);
        [$response, $status] = $client->CreateCover($optimizeReq)->wait();
        if ($status->code !== 0) {
            VarDumper::dump($status);
            $media->status = Media::STATUS_FAILED;
            $media->save($withoutTouch);
            throw new Exception(__CLASS__ . PHP_EOL . 'Cannot do video cover');
        }
        $media->addToSet('processing_log', '8. Video Cover generating: Ok');
        $media->save($withoutTouch);

        $uploadReq->setSrc($cover);
        $uploadReq->setDes('covers/'.str_replace('.mp4', '.jpg', $mp4ConvertedFile));
        [$response, $status] = $client->UploadCover($uploadReq)->wait();
        if ($status->code !== 0) {
            VarDumper::dump($status);
            $media->status = Media::STATUS_FAILED;
            $media->save($withoutTouch);
            throw new Exception(__CLASS__ . PHP_EOL . 'Cannot do cover upload');
        }
        $media->addToSet('processing_log', '9. Video Cover uploading: Ok');
        $media->save($withoutTouch);

        $i = 10;
        foreach ($toRemoveFiles as $toRemoveFile) {
            $removeReq = new RemoveRequest();
            $removeReq->setFile($toRemoveFile);
            [$response, $status] = $client->Remove($removeReq)->wait();
            if ($status->code !== 0) {
                VarDumper::dump($status);
                $media->status = Media::STATUS_FAILED;
                $media->save($withoutTouch);
                throw new Exception(__CLASS__ . PHP_EOL . 'Cannot remove ' . $toRemoveFile);
            }
            $media->addToSet('processing_log', ++$i.'. Remove files: '.$toRemoveFile);
        }

        $media->file = $mp4ConvertedFile;
        $media->status = $keepStatus ?? Media::STATUS_COMPLETED;
        $touchWithTrue = [
            'status' => true,
            'length' => true,
            'processing_log' => true,
            'file' => true,
        ];
        $media->save($touchWithTrue);

        $media->refresh();
        WsChannel::Push($media->creator['_id'], 'media.create', [
            'media' => $media->simple_array,
            'message' => 'All done',
            'progress' => 100,
        ]);

        $msg = "New post from is waiting for moderation {$media->slack_admin_url}.";
        $msg .= PHP_EOL.'_*Log:*_ '.PHP_EOL.implode("\n", $media->processing_log);
        $msg .= PHP_EOL.'_*Errors:*_ '.PHP_EOL.implode("\n", $media->firstErrors);

        return (new SlackMessage())
            ->from('Reporter', ':vhs:')
            ->to('#waptap-report')
            ->content($msg);
    }

    public function failed(Throwable $exception): void
    {
        $this->user->notify(new JobFailed(self::class, $this->attempts(), $exception->getMessage()));
    }
}
