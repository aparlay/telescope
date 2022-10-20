<?php

namespace Aparlay\Core\Commands;

use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Models\Media;
use Illuminate\Console\Command;
use Illuminate\Http\Response;

class VideoScoreHourlyCommand extends Command
{
    public $signature = 'video:score-hourly';

    public $description = 'This command is responsible for update video score hourly';

    public function handle()
    {
        $highestScore = Media::where('sort_score', ['$exists' => true])
            ->date(null, DT::utcDateTime(['d' => -1]))
            ->orderBy('sort_score', 'desc')
            ->first()
            ->sort_score;
        $mediaQuery = Media::where('is_fake', ['$exists' => false])
            ->date(DT::utcDateTime(['d' => -1]), DT::utcNow())
            ->availableForFollower();
        foreach ($mediaQuery->get() as $media) {
            $media->sort_score = $highestScore + $media->awesomeness_score + $media->beauty_score;
            $media->sort_score += ($media->time_score / 2);
            $media->sort_score += ($media->like_score / 3);
            $media->sort_score += ($media->visit_score / 3);

            $msg = '<fg=yellow;options=bold>';
            $msg .= $media->_id.' score set to '.$media->sort_score.'</>';
            $msg .= PHP_EOL;
            $this->line($msg);

            $media->save();
        }

        return self::SUCCESS;
    }
}
