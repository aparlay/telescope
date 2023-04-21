<?php

namespace Aparlay\Core\Commands;

use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Jobs\ReprocessMedia;
use Aparlay\Core\Models\Enums\MediaStatus;
use Aparlay\Core\Models\Media;
use Illuminate\Console\Command;

class VideoReprocessCommand extends Command
{
    public $signature   = 'video:reprocess';
    public $description = 'This command is responsible for update video reprocess';

    public function handle()
    {
        $mediaQuery = Media::where(['is_fake' => ['$exists' => false]])
            ->date(null, DT::utcDateTime(['m' => -3]))
            ->status(MediaStatus::UPLOADED->value);
        foreach ($mediaQuery->get() as $media) {
            ReprocessMedia::dispatch($media->_id, $media->file)->onQueue('low');

            $media->status = MediaStatus::QUEUED->value;
            $media->save();

            $msg           = '<fg=yellow;options=bold>';
            $msg .= 'Video ' . $media->_id . ' need to send for reprocessing' . '</>';
            $msg .= PHP_EOL;
            $this->line($msg);
        }

        return self::SUCCESS;
    }
}
