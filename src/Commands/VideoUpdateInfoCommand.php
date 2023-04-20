<?php

namespace Aparlay\Core\Commands;

use Aparlay\Core\Jobs\UpdateMediaInfo;
use Aparlay\Core\Models\Media;
use Illuminate\Console\Command;

class VideoUpdateInfoCommand extends Command
{
    public $signature   = 'video:update_info';
    public $description = 'This command is responsible for update video information.';

    public function handle()
    {
        $mediaQuery = Media::Where(['is_fake' => ['$exists' => false]]);

        foreach ($mediaQuery->get() as $media) {
            UpdateMediaInfo::dispatch($media->creator['_id'], $media->_id, $media->file)->onQueue('low');

            $msg = '<fg=yellow;options=bold>';
            $msg .= 'Video ' . $media->_id . ' ' . $media->file . ' need to send for update info.' . '</>';
            $msg .= PHP_EOL;
            $this->line($msg);
        }

        return self::SUCCESS;
    }
}
