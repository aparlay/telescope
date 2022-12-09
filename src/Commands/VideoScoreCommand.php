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
        foreach ($mediaQuery->get() as $media) {
            /** @var Media $media */
            $media->recalculateSortScores();
        }

        $rows = [];
        foreach ($mediaQuery->orderBy('sort_scores.default', 'DESC')->get() as $media) {
            $rows[] = [
                $media->_id,
                $media->awesomeness_score,
                $media->beauty_score,
                $media->skin_score,
                $media->time_score,
                $media->like_score,
                $media->visit_score,
                $media->sort_scores['default'],
                $media->sort_scores['guest'],
                $media->sort_scores['returned'],
                $media->sort_scores['registered'],
            ];
            $media->save();
        }

        $headers = ['id', 'awesomeness', 'beauty', 'skin', 'time', 'like', 'watch', 'default', 'guest', 'returned', 'registered'];
        $this->table($headers, $rows);

        return self::SUCCESS;
    }
}
