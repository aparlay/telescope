<?php

namespace Aparlay\Core\Commands;

use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Models\Media;
use Illuminate\Console\Command;

class VideoScoreHourlyCommand extends Command
{
    public $signature   = 'video:score-hourly';
    public $description = 'This command is responsible for update video score hourly';

    public function handle()
    {
        Media::where('is_fake', ['$exists' => false])
            ->date(DT::utcDateTime(['d' => -1]), DT::utcNow())
            ->availableForFollower()
            ->chunk(200, function ($models) {
                foreach ($models as $media) {
                    /** @var Media $media */
                    $media->recalculateSortScores();
                }
            });

        return self::SUCCESS;
    }
}
