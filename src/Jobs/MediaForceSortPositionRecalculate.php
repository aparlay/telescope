<?php

namespace Aparlay\Core\Jobs;

use Aparlay\Core\Api\V1\Services\MediaService;
use Aparlay\Core\Models\Enums\MediaSortCategories;
use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\User;
use Aparlay\Core\Notifications\JobFailed;

use function Clue\StreamFilter\fun;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
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
    public $backoff = [5, 10, 15];

    /**
     * Create a new job instance.
     *
     * @return void
     *
     * @throws Exception
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
                ->orderBy('force_sort_positions.'.$category)
                ->select(['_id'])
                ->get()
                ->pluck(['_id'])
                ->map(fn ($mediaId) => new ObjectId($mediaId))
                ->toArray();
            if (empty($forcedPositionMediaIds)) {
                continue;
            }

            $forcedMedias = Media::availableForFollower()
                ->hasForceSortPosition($category)
                ->orderBy('force_sort_positions.'.$category)
                ->get();
            $forcedPositionMax = $forcedMedias->last()->force_sort_positions[$category];

            $neighborMedias = Media::public()
                ->confirmed()
                ->sort($category)
                ->whereNotIn('_id', $forcedPositionMediaIds)
                ->limit($forcedPositionMax)
                ->get();
            $topScore = $neighborMedias->first()->sort_scores[$category];
            $bottomScore = $neighborMedias->first()->sort_scores[$category];
            $stepScore = 0.00001; //($topScore-$bottomScore) / (count($neighborMedias)+count($forcedMedias));

            $position = 1;
            while ($position <= $forcedPositionMax) {
                foreach ($forcedMedias as $forcedMedia) {
                    if ($position === (int) $forcedMedia->force_sort_positions[$category]) {
                        $medias[$position] = $forcedMedia;
                    }
                }

                if (! isset($medias[$position])) {
                    $medias[$position] = $neighborMedias->shift();
                }

                $position++;
            }

            $lowestScore = $bottomScore - $stepScore;
            while ($position-- > 1) {
                $sortScores = $medias[$position]->sort_scores;
                $lowestScore += $stepScore;
                $sortScores[$category] = $lowestScore;
                $medias[$position]->sort_scores = $sortScores;
                $medias[$position]->save();
                $medias[$position]->storeInGeneralCaches();
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
