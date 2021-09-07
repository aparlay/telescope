<?php

namespace Aparlay\Core\Commands;

use Aparlay\Core\Jobs\UpdateMediaInfo;
use Aparlay\Core\Models\Media;
use Illuminate\Console\Command;

class VideoUpdateInfoCommand extends Command
{
    public $signature = 'video:updateInfo';

    public $description = 'Video Update Information';

    public function handle()
    {
        $mediaQuery = Media::Where(['is_fake' => ['$exists' => false]]);

        foreach ($mediaQuery->get() as $media) {
            UpdateMediaInfo::dispatch($media->creator['_id'], $media->file, $media->_id)->onQueue('lowpriority');

            $this->line('<fg=yellow;options=bold>'.'Video '.$media->_id.' '.$media->file.' need to send for update info.'.PHP_EOL.'</>');
        }
    }
}
