<?php

namespace Aparlay\Core\Jobs;

use Aparlay\Core\Helpers\Cdn;
use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\User;
use Aparlay\Core\Notifications\JobFailed;
use Aparlay\Core\Notifications\ThirdPartyLogger;
use ErrorException;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use MongoDB\BSON\ObjectId;
use Str;
use Throwable;

class PurgeMediaJob implements ShouldQueue
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
    public int $maxExceptions = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int|array
     */
    public $backoff = 10;

    /**
     * Create a new job instance.
     *
     * @return void
     * @throws Exception
     */
    public function __construct(public string $mediaId)
    {
        $this->onQueue('low');
    }

    public function middleware()
    {
        return [new WithoutOverlapping($this->mediaId)];
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        $media = Media::media($this->mediaId)->first();
        if ($media === null) {
            throw new Exception(__CLASS__.PHP_EOL.'The requested media with id not found: '.$this->media_id);
        }

        if (Storage::disk('gc-videos')->fileExists($media->file)) {
            Storage::disk('gc-videos')->rename($media->file, $media->delete_prefix.$media->file);
        }

        if (Storage::disk('gc-covers')->fileExists($media->cover_file)) {
            Storage::disk('gc-covers')->rename($media->cover_file, $media->delete_prefix.$media->cover_file);
        }

        BunnyCdnPurgeUrlJob::dispatch($this->mediaId);
    }

    public function failed(Throwable $exception): void
    {
        if (($user = User::admin()->first()) !== null) {
            $user->notify(new JobFailed(self::class, $this->attempts(), $exception->getMessage()));
        }
    }
}
