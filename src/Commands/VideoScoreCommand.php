<?php

namespace Aparlay\Core\Commands;

use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Models\Media;
use Illuminate\Console\Command;

class VideoScoreCommand extends Command
{
    public $signature = 'video:score';

    public $description = 'This command is responsible for update video score';

    public function handle()
    {
        $mediaQuery = Media::Where(['is_fake' => ['$exists' => false]])->availableForFollower()->orderBy('created_at', 'ASC');
        $highestScore = Media::date(null, DT::utcDateTime(['d' => -1]))->orderBy('sort_scores.default', 'DESC')->first()->sort_score;
        foreach ($mediaQuery->get() as $media) {
            $media->recalculateSortScores();
        }

        $rows = [];
        foreach ($mediaQuery->orderBy('sort_scores.default', 'DESC')->get() as $media) {
            $rows[] = [
                $media->_id,
                $highestScore,
                $media->awesomeness_score,
                $media->beauty_score,
                $media->time_score,
                $media->like_score,
                $media->visit_score,
                'https://app.waptap.dev/share/'.$media->_id,
            ];
            $media->save();
        }

        $headers = ['id', 'total', 'highest', 'awesomeness', 'beauty', 'time', 'like', 'watch', 'link'];
        $this->table($headers, $rows);

        return self::SUCCESS;
    }
}
