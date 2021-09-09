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
            // ->date(null, DT::utcDateTime(['m' => -3]))
            ->status(Media::STATUS_UPLOADED);
        foreach ($mediaQuery->get() as $media) {
            ReprocessMedia::dispatch($media->_id, $media->file)->onQueue('lowpriority');

            $media->status = Media::STATUS_QUEUED;
            $media->save();

            $this->line('<fg=yellow;options=bold>'.'Video '.$media->_id.' need to send for reprocessing.'.PHP_EOL.'</>');
        }
    }
}
