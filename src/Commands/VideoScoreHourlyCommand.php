<?php

namespace Aparlay\Core\Commands;

use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Models\Media;
use Illuminate\Console\Command;
use Illuminate\Http\Response;

class VideoScoreHourlyCommand extends Command
{
    public $signature = 'video:scoreHourly';

    public $description = 'Video Score Hourly';

    public function handle()
    {
        $mediaQuery = Media::Where(['is_fake' => ['$exists' => false]])
            ->date(DT::utcDateTime(['d' => -1]), DT::utcNow())
            ->availableForFollower();
        foreach ($mediaQuery->get() as $media) {
            $media->sort_score = $media->awesomeness_score + ($media->time_score / 2) + ($media->like_score / 3) + ($media->visit_score / 3);
            $this->line('<fg=yellow;options=bold>'.$media->_id.' score set to '.$media->sort_score.PHP_EOL.'</>');
            $media->save();
        }

        $this->info(Response::HTTP_OK);
    }
}
