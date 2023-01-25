<?php

namespace Aparlay\Core\Commands;

use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Jobs\MediaForceSortPositionRecalculate;
use Aparlay\Core\Models\Media;
use Illuminate\Console\Command;
use Illuminate\Http\Response;

class VideoScoreDailyCommand extends Command
{
    public $signature = 'video:score-daily';

    public $description = 'This command is responsible for update video score daily';

    public function handle()
    {
        $mediaQuery = Media::where('is_fake', ['$exists' => false])
            ->date(DT::utcDateTime(['d' => -6]), DT::utcDateTime(['d' => -1]))
            ->availableForFollower();
        $bar = $this->output->createProgressBar($mediaQuery->count());
        foreach ($mediaQuery->lazy() as $media) {
            /** @var Media $media */
            $media->recalculateSortScores();
            $media->save();
            $media->refresh();
            $media->storeInGeneralCaches();
            $bar->advance();
        }

        $bar->finish();
        MediaForceSortPositionRecalculate::dispatch();

        Media::CachePublicExplicitMediaIds();
        Media::CachePublicToplessMediaIds();
        Media::CachePublicMediaIds();

        return self::SUCCESS;
    }
}
