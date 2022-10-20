<?php

namespace Aparlay\Core\Commands;

use Aparlay\Core\Models\Media;
use Illuminate\Console\Command;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class VideoScoreCommand extends Command
{
    public $signature = 'video:score';

    public $description = 'This command is responsible for update video score';

    public function handle()
    {
        $mediaQuery = Media::Where(['is_fake' => ['$exists' => false]])->availableForFollower();
        $highestScore = Media::orderBy('sort_score', 'desc')->first()->sort_score;
        foreach ($mediaQuery->get() as $media) {
            $msg = '<fg=blue;options=bold>';
            $msg .= '--------------------------------'.'</>';
            $msg .= PHP_EOL;
            $this->line($msg);

            if ($media->created_at->getTimestamp() > Carbon::yesterday()->getTimestamp()) {
                $media->sort_score = $highestScore + $media->awesomeness_score + $media->beauty_score;
            } else {
                $media->sort_score = $media->awesomeness_score + $media->beauty_score;
            }
            $media->sort_score += ($media->time_score / 2);
            $media->sort_score += ($media->like_score / 3);
            $media->sort_score += ($media->visit_score / 3);

            $msg1 = '<fg=yellow;options=bold>';
            $msg1 .= '  - awesomeness_score set to '.$media->awesomeness_score.'</>';
            $msg1 .= PHP_EOL;
            $this->line($msg1);

            $msg1 = '<fg=yellow;options=bold>';
            $msg1 .= '  - beauty_score set to '.$media->beauty_score.'</>';
            $msg1 .= PHP_EOL;
            $this->line($msg1);

            $msg2 = '<fg=yellow;options=bold>';
            $msg2 .= '  - time_score set to '.$media->time_score.'</>';
            $msg2 .= PHP_EOL;
            $this->line($msg2);

            $msg3 = '<fg=yellow;options=bold>';
            $msg3 .= '  - like_score set to '.$media->like_score.'</>';
            $msg3 .= PHP_EOL;
            $this->line($msg3);

            $msg4 = '<fg=yellow;options=bold>';
            $msg4 .= '  - visit_score set to '.$media->visit_score.'</>';
            $msg4 .= PHP_EOL;
            $this->line($msg4);

            $msg5 = '<fg=yellow;options=bold>';
            $msg5 .= '  - total set to '.$media->sort_score.'</>';
            $msg5 .= PHP_EOL;
            $this->line($msg5);

            $msg6 = '<fg=yellow;options=bold>';
            $msg6 .= '  - link https://app.waptap.dev/share/'.$media->_id.'</>';
            $msg6 .= PHP_EOL;
            $this->line($msg6);

            $media->save();
        }

        $rows = [];
        foreach ($mediaQuery->orderBy('sort_score', 'DESC')->get() as $media) {
            $rows[] = [
                $media->_id,
                $media->sort_score,
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
