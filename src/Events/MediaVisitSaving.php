<?php

namespace Aparlay\Core\Events;

use Aparlay\Core\Models\MediaVisit;
use Exception;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MediaVisitSaving
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     * @throws Exception
     */
    public function __construct(MediaVisit $mediaVisit)
    {
        ModelSaving::dispatch($mediaVisit);
        $mediaVisit->push('media_ids', $mediaVisit->media_id, true);
    }
}
