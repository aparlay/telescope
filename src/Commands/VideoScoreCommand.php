<?php

namespace Aparlay\Core\Commands;

use Aparlay\Core\Models\Media;
use Illuminate\Console\Command;
use Illuminate\Http\Response;

class VideoScoreCommand extends Command
{
    public $signature = 'video:score';

    public $description = 'Video Score';

    public function handle()
    {
        $mediaQuery = Media::Where(['is_fake' => ['$exists' => false]])->availableForFollower();
        
        foreach ($mediaQuery->get() as $media) {

            $this->line('<fg=blue;options=bold>' . '--------------------------------' . PHP_EOL . '</>');
            
            $media->sort_score = $media->awesomeness_score + ($media->time_score / 2) + ($media->like_score / 3) + ($media->visit_score / 3);
            
            $this->line('<fg=yellow;options=bold>' . '  - awesomeness_score set to ' . $media->awesomeness_score . PHP_EOL . '</>');
            $this->line('<fg=yellow;options=bold>' . '  - time_score set to ' . $media->time_score . PHP_EOL . '</>');
            $this->line('<fg=yellow;options=bold>' . '  - like_score set to ' . $media->like_score . PHP_EOL . '</>');
            $this->line('<fg=yellow;options=bold>' . '  - visit_score set to ' . $media->visit_score . PHP_EOL . '</>');
            $this->line('<fg=yellow;options=bold>' . '  - total set to ' . $media->sort_score . PHP_EOL . '</>');
            $this->line('<fg=yellow;options=bold>' . '  - link https://app.waptap.dev/share/' . $media->_id . PHP_EOL . '</>');
            $media->save();
        }

        $rows = [];
        foreach ($mediaQuery->orderBy('sort_score', 'DESC')->get() as $media) {
            $rows[] = [
                $media->_id,
                $media->sort_score,
                $media->awesomeness_score,
                $media->time_score,
                $media->like_score,
                $media->visit_score,
                'https://app.waptap.dev/share/' . $media->_id
            ];
            $media->save();
        }

        $headers = ['id', 'total', 'awesomeness', 'time', 'like', 'watch', 'link'];
        $this->table($headers, $rows);

        $this->info(Response::HTTP_OK);
    }
}
