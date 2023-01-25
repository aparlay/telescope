<?php

namespace Aparlay\Core\Commands;

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
            $media->recalculateSortScores();
            $bar->advance();
        }

        $bar->finish();

        Media::CachePublicExplicitMediaIds();
        Media::CachePublicToplessMediaIds();
        Media::CachePublicMediaIds();
        return self::SUCCESS;
    }
}
