<?php

namespace Aparlay\Core\Events;

use Aparlay\Core\Jobs\UploadMedia;
use Aparlay\Core\Models\Media;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MediaCreated
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Media $media)
    {
        //dispatch((new UploadMedia((string) $media->userObj->_id, (string) $media->_id, $media->file))->onQueue('high'));
    }
}
