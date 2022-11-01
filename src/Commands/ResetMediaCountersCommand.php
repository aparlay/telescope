<?php

namespace Aparlay\Core\Commands;

use Aparlay\Core\Models\Media;
use Illuminate\Console\Command;

class ResetMediaCountersCommand extends Command
{
    public $signature = 'counter:media';

    public $description = 'Aparlay Update Media Counters';

    public function handle()
    {
        foreach (Media::where('is_fake', ['$exists' => false])->lazy() as $media) {
            /** @var Media $media */
            $media->updateLikes();
            $media->updateVisits();
            $media->updateComments();

            $this->info('Media '.$media->_id.' has been updated');
        }
        $this->comment('All done');

        return self::SUCCESS;
    }
}
