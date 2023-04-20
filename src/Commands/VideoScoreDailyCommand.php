<?php

namespace Aparlay\Core\Commands;

use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Models\Media;
use Illuminate\Console\Command;

class VideoScoreDailyCommand extends Command
{
    public $signature   = 'video:score-daily';
    public $description = 'This command is responsible for update video score daily';

    public function handle()
    {
        Media::where('is_fake', ['$exists' => false])
            ->date(DT::utcDateTime(['d' => -6]), DT::utcDateTime(['d' => -1]))
            ->availableForFollower()
            ->chunk(200, function ($models) {
                foreach ($models as $media) {
                    $media->recalculateSortScores();
                }
            });

        Media::CachePublicExplicitMediaIds();
        Media::CachePublicToplessMediaIds();
        Media::CachePublicMediaIds();

        return self::SUCCESS;
    }
}
