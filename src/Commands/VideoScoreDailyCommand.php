<?php

namespace Aparlay\Core\Commands;

use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Models\Media;
use Illuminate\Console\Command;
use Illuminate\Http\Response;

class VideoScoreDailyCommand extends Command
{
    public $signature = 'video:score-daily';

    public $description = 'This command is responsible for update video score daily';

    public function handle()
    {
        Media::where('is_fake', ['$exists' => false])
            ->date(DT::utcDateTime(['d' => -6]), DT::utcDateTime(['d' => -1]))
            ->availableForFollower()
            ->chunk(200, function ($models) {
                foreach ($models as $media) {
                    $sortScore = $media->awesomeness_score;
                    $sortScore += ($media->time_score / 2);
                    $sortScore += ($media->like_score / 3);
                    $sortScore += ($media->visit_score / 3);

                    if (((int)$media->sort_score - 1) < $sortScore) {
                        $msg = '<fg=yellow;options=bold>';
                        $msg .= $media->_id.' score set to '.$media->sort_score.'</>';
                        $msg .= PHP_EOL;
                        $this->line($msg);

                        $media->save();
                    }

                }
            });

        return self::SUCCESS;
    }
}
