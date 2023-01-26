<?php

namespace Aparlay\Core\Jobs;

use Aparlay\Core\Api\V1\Services\MediaService;
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
use Illuminate\Support\Facades\Log;
use MongoDB\BSON\ObjectId;
use Throwable;

class MediaForceSortPositionRecalculator implements ShouldQueue
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
            $mediaQuery = Media::availableForFollower()
                ->orderBy('force_sort_positions.'.$category)
                ->hasForceSortPosition($category);
            foreach ($mediaQuery->lazy() as $media) {
                /** @var Media $media */
                $sortScores = $media->sort_scores;
                if ($media->force_sort_positions[$category] >= 2) {
                    $sortScores[$category] = Media::public()
                        ->where('_id', '!=', new ObjectId($media->_id))
                        ->confirmed()
                        ->sort($category)
                        ->limit(2)
                        ->offset($media->force_sort_positions[$category] - 2)
                        ->select(['sort_scores.'.$category])
                        ->get()
                        ->pluck('sort_scores.'.$category)
                        ->avg() ?? $sortScores[$category];
                } else {
                    $sortScores[$category] = (Media::public()
                            ->where('_id', '!=', new ObjectId($media->_id))
                            ->confirmed()
                            ->sort($category)
                            ->first()
                            ->sort_scores[$category] ?? $sortScores[$category] + PHP_FLOAT_MIN) - PHP_FLOAT_MIN;
                }

                Log::warning("Media: {$media->_id} for {$category}\n".var_export($media->sort_scores,true)."\n".var_export($sortScores, true));
                $media->sort_scores = $sortScores;
                $media->save();
                $media->storeInGeneralCaches();
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
