<?php

namespace Aparlay\Core\Jobs;

use Aparlay\Core\Models\User;
use Aparlay\Core\Notifications\JobFailed;
use Exception;
use GeoIp2\WebService\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use MongoDB\BSON\ObjectId;
use Throwable;

class UpdateUserCountry implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    public User $user;
    public string $ip;
    public string $userId;

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
    public $backoff           = 30;

    /**
     * Create a new job instance.
     *
     * @throws Exception
     *
     * @return void
     */
    public function __construct(string $userId, string $ip)
    {
        $this->onQueue('low');
        $this->ip     = $ip;
        $this->userId = $userId;
        User::findOrFail(new ObjectId($userId));
    }

    /**
     * Execute the job.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *
     * @return void
     */
    public function handle()
    {
        $user   = User::findOrFail(new ObjectId($this->userId));
        $client = new Client(config('app.maxmind.accountId'), config('app.maxmind.licenseKey'), ['en']);
        $record = $client->city($this->ip);

        if (isset($record->country->isoCode) && !empty($record->country->isoCode)) {
            $user->country_alpha2 = Str::lower($record->country->isoCode);
            $user->last_location  = [
                'lat' => $record->location->latitude,
                'lng' => $record->location->longitude,
            ];
            $user->save();
        }
    }

    public function failed(Throwable $exception)
    {
        if (($user = User::admin()->first()) !== null) {
            $user->notify(new JobFailed(self::class, $this->attempts(), $exception->getMessage()));
        }
    }
}
