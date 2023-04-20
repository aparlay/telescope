<?php

namespace Aparlay\Core\Jobs;

use Aparlay\Core\Models\User;
use Aparlay\Core\Notifications\JobFailed;
use Aparlay\Core\Notifications\ThirdPartyLogger;
use ErrorException;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Filesystem\FileExistsException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\RequestException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Log;
use Str;
use Throwable;

class KeitaroPostback implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries         = 30;

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
    public function __construct(
        public string $subId,
        public string $token,
        public string $postbackCode = '4f8b439',
        public string $trackerUrl = 'https://track.waptap.com/'
    ) {
    }

    /**
     * Execute the job.
     *
     * @throws FileExistsException
     * @throws FileNotFoundException
     *
     * @return void
     */
    public function handle()
    {
        try {
            $url      = $this->trackerUrl . $this->postbackCode . '/postback';
            $params   = [
                'subid' => $this->subId,
                'status' => 'lead',
                'payout' => 0,
            ];
            $response = Http::timeout(240)
                ->retry(5, 60000, function ($exception, $request) use ($url, $params) {
                    if (!Str::startsWith($exception->response->status(), '2')) {
                        User::admin()->first()->notify(
                            new ThirdPartyLogger(
                                '',
                                self::class,
                                $url,
                                ['body' => $params],
                                ['status' => $exception->response->status(), 'body' => $exception->response->json()]
                            )
                        );

                        return false;
                    }

                    return true;
                }, false)
                ->post($url, $params);

            if ($response->failed()) {
                User::admin()->first()->notify(new ThirdPartyLogger('', self::class, $url, $params, $response->json()));
            }

            return $response->json();
        } catch (RequestException $e) {
            $responseBodyAsString = $e->response->body();
            Log::error('Masspay request error: ' . $responseBodyAsString);

            throw new ErrorException($responseBodyAsString);
        }
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
        if (($user = User::admin()->first()) !== null) {
            $user->notify(new JobFailed(self::class, $this->attempts(), $exception->getMessage()));
        }
    }
}
