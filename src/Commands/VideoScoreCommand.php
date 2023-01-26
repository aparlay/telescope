<?php

namespace Aparlay\Core\Commands;

use Aparlay\Core\Jobs\MediaForceSortPositionRecalculator;
use Aparlay\Core\Models\Media;
use Illuminate\Console\Command;
use Psr\SimpleCache\InvalidArgumentException;

class VideoScoreCommand extends Command
{
    public $signature = 'video:score';

    public $description = 'This command is responsible for update video score';

    /**
     * @throws InvalidArgumentException
     */
    public function handle()
    {
        $mediaQuery = Media::availableForFollower()->whereNull('is_fake')->orderBy('created_at', 'ASC');
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

        MediaForceSortPositionRecalculator::dispatch();

        return self::SUCCESS;
    }
}
