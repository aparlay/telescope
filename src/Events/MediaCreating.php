<?php

namespace Aparlay\Core\Events;

use Aparlay\Core\Models\Media;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MediaCreating
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return false|void
     */
    public function __construct(Media $media)
    {
    }
}
