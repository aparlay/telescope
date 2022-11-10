<?php

namespace Aparlay\Core\Jobs;

use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\User;
use Aparlay\Core\Notifications\JobFailed;
use Aparlay\Core\Notifications\ThirdPartyLogger;
use ErrorException;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\RequestException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use MongoDB\BSON\ObjectId;
use Str;
use Throwable;

class BunnyCdnPurgeUrlJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public string $media_id;

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
    public function __construct(string|ObjectId $mediaId)
    {
        $this->onQueue('low');

        $this->media_id = (string) $mediaId;
    }

    public function middleware()
    {
        return [new WithoutOverlapping($this->media_id)];
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        $media = Media::media($this->media_id)->first();
        if ($media === null) {
            throw new Exception(__CLASS__.PHP_EOL.'Bunny CDN Url Purge not found the requested media with id '.$this->media_id);
        }

        $this->purge([$media->cover_url, $media->file_url]);
    }

    public function failed(Throwable $exception): void
    {
        if (($user = User::admin()->first()) !== null) {
            $user->notify(new JobFailed(self::class, $this->attempts(), $exception->getMessage()));
        }
    }

    private function purge($urls)
    {
        try {
            $response = Http::timeout(180)
                ->retry(5, 60000, function ($exception, $request) {
                    if (! Str::startsWith($exception->response->status(), '2')) {
                        User::admin()->first()->notify(new ThirdPartyLogger(
                            '',
                            self::class,
                            'https://api.bunny.net/purge',
                            [],
                            ['status' => $exception->response->status(), 'body' => $exception->response->json()]
                        ));

                        return false;
                    }

                    return true;
                }, false)
                ->withHeaders([
                    'AccessKey' => config('app.bunny.api_access_key'),
                    'accept' => 'application/json',
                ])
                ->post('https://api.bunny.net/purge', [
                    'async' => true,
                    'urls' => $urls,
                ]);

            if ($response->failed()) {
                User::admin()
                    ->first()
                    ->notify(
                        new ThirdPartyLogger(
                            '',
                            self::class,
                            'https://api.bunny.net/purge',
                            ['async' => true, 'urls' => $urls],
                            $response->json()
                        )
                    );
            }

            return $response->json();
        } catch (RequestException $e) {
            $responseBodyAsString = $e->response->body();
            \Log::error('Bunny CDN request error: '.$responseBodyAsString);
            throw new ErrorException($responseBodyAsString);
        }
    }
}
