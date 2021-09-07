<?php

namespace Aparlay\Core\Commands;

use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Jobs\ReprocessMedia;
use Aparlay\Core\Models\Media;
use Illuminate\Console\Command;

class VideoReprocessCommand extends Command
{
    public $signature = 'video:reprocess';

    public $description = 'Video Reprocess';

    public function handle()
    {
        $mediaQuery = Media::Where(['is_fake' => ['$exists' => false]])
            ->date(null, DT::utcDateTime(['m' => -3]))
            ->status(Media::STATUS_UPLOADED);
        foreach ($mediaQuery->get() as $media) {
            ReprocessMedia::dispatch([
                'file' => $media->file,
                'media_id' => $media->_id
            ])->onQueue('lowpriority');

            $media->status = Media::STATUS_QUEUED;
            $media->save(false, ['status']);

            $this->line('<fg=yellow;options=bold>' . 'Video ' . $media->_id . ' need to send for reprocessing.' . PHP_EOL . '</>');
        }
    }
}
