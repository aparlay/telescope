<?php

namespace Aparlay\Core\Jobs;

use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Models\Hashtag;
use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\MediaLike;
use Aparlay\Core\Models\User;
use Aparlay\Core\Notifications\JobFailed;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;
use MongoDB\BSON\ObjectId;
use Throwable;

class RecalculateHashtag implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public string $tag;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 10;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     */
    public int $maxExceptions = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int|array
     */
    public $backoff = [60, 300, 1800, 3600];

    /**
     * Create a new job instance.
     *
     * @return void
     *
     * @throws Exception
     */
    public function __construct(string $tag)
    {
        $this->onQueue('low');
        $this->tag = $tag;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $hashtag = Hashtag::firstOrCreate(['tag' => $this->tag]);
        $count = Media::hashtag($this->tag)->public()->confirmed()->count();

        if ($count === 0) {
            $hashtag->delete();
            return;
        }

        $hashtag->media_count = $count;
        $hashtag->like_count = Media::hashtag($this->tag)->public()->confirmed()->sum('like_count');
        $hashtag->visit_count = Media::hashtag($this->tag)->public()->confirmed()->sum('visit_count');
        $hashtag->sort_score = (Media::hashtag($this->tag)->public()->confirmed()->sum('sort_score') / $count);
        $hashtag->save();
    }

    public function failed(Throwable $exception): void
    {
        if (($user = User::admin()->first()) !== null) {
            $user->notify(new JobFailed(self::class, $this->attempts(), $exception->getMessage()));
        }
    }
}
