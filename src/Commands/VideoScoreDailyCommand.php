<?php

namespace Aparlay\Core\Commands;

use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Models\Media;
use Illuminate\Console\Command;
use Illuminate\Http\Response;

class VideoScoreDailyCommand extends Command
{
    public $signature = 'video:scoreDaily';

    public $description = 'Video Score Daily';

    public function handle()
    {
        $mediaQuery = Media::Where(['is_fake' => ['$exists' => false]])
            ->date(DT::utcDateTime(['d' => -6]), DT::utcDateTime(['d' => -1]))
            ->availableForFollower();
        foreach ($mediaQuery->get() as $media) {
            $media->sort_score = $media->awesomeness_score + ($media->time_score / 2) + ($media->like_score / 3) + ($media->visit_score / 3);

            $this->line('<fg=yellow;options=bold>'.$media->_id.' score set to '.$media->sort_score.PHP_EOL.'</>');

            $media->save();
        }

        $this->info(Response::HTTP_OK);
    }
}
