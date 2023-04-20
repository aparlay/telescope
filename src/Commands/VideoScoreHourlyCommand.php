<?php

namespace Aparlay\Core\Commands;

use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Jobs\MediaForceSortPositionRecalculate;
use Aparlay\Core\Models\Media;
use Illuminate\Console\Command;

class VideoScoreHourlyCommand extends Command
{
    public $signature   = 'video:score-hourly';
    public $description = 'This command is responsible for update video score hourly';

    public function handle()
    {
        $mediaQuery = Media::where('is_fake', ['$exists' => false])
            ->date(DT::utcDateTime(['d' => -1]), DT::utcNow())
            ->availableForFollower();
        $bar        = $this->output->createProgressBar($mediaQuery->count());
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

        return self::SUCCESS;
    }
}
