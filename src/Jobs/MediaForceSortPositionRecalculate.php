<?php

namespace Aparlay\Core\Jobs;

use Aparlay\Core\Models\Enums\MediaSortCategories;
use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\User;
use Aparlay\Core\Notifications\JobFailed;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use MongoDB\BSON\ObjectId;
use Throwable;

class MediaForceSortPositionRecalculate implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

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
    public $backoff           = [5, 10, 15];

    /**
     * Create a new job instance.
     *
     * @throws Exception
     *
     * @return void
     */
    public function __construct()
    {
        $this->onQueue('low');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach (MediaSortCategories::getAllValues() as $category) {
            $forcedPositionMediaIds = Media::availableForFollower()
                ->hasForceSortPosition($category)
                ->orderBy('force_sort_positions.' . $category)
                ->select(['_id'])
                ->get()
                ->pluck(['_id'])
                ->toArray();
            if (empty($forcedPositionMediaIds)) {
                continue;
            }
            $forcedMedias           = Media::availableForFollower()
                ->hasForceSortPosition($category)
                ->orderBy('force_sort_positions.' . $category)
                ->get();
            foreach ($forcedMedias as $forcedMedia) {
                $position                 = $forcedMedia->force_sort_positions[$category];
                $locatedMediaInPosition   = Media::public()
                    ->confirmed()
                    ->sort($category) // desc
                    ->whereNotIn('_id', collect($forcedPositionMediaIds)->map(fn ($mediaId) => new ObjectId($mediaId))->toArray())
                    ->offset($position - 1)
                    ->first();
                $sortScores               = $forcedMedia->sort_scores;
                $sortScores[$category]    = $locatedMediaInPosition->sort_scores[$category] + (0.0000001 * count($forcedPositionMediaIds));
                $forcedMedia->sort_scores = $sortScores;
                $forcedMedia->save();
                $forcedMedia->storeInGeneralCaches();

                $forcedPositionMediaIds   = array_diff($forcedPositionMediaIds, [(string) $forcedMedia->_id]);
            }
        }
    }

    public function failed(Throwable $exception): void
    {
        if (($user = User::admin()->first()) !== null) {
            $user->notify(new JobFailed(self::class, $this->attempts(), $exception->getMessage()));
        }
    }
}
