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
        foreach ($mediaQuery->lazy() as $media) {/** @var Media $media */
            $media->recalculateSortScores();
        }

        $rows = [];
        foreach ($mediaQuery->orderBy('sort_scores.default', 'DESC')->lazy() as $media) {
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
        }

        $headers = ['id', 'awesomeness', 'beauty', 'skin', 'time', 'like', 'watch', 'default', 'guest', 'returned', 'registered'];
        $this->table($headers, $rows);

        return self::SUCCESS;
    }
}
