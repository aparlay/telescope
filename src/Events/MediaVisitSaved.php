<?php

namespace Aparlay\Core\Events;

use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Models\MediaVisit;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MediaVisitSaved
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(MediaVisit $mediaVisit)
    {
        if ($mediaVisit->wasChanged('media_ids')) {
            $media = $mediaVisit->mediaObj;

            if ($mediaVisit->duration > ($media->length / 4)) {
                if ($mediaVisit->duration <= $media->length) {
                    $media->length_watched += $mediaVisit->duration;
                }
                $media->visit_count++;
                $media->addToSet('visits', ['_id' => $mediaVisit->userObj->_id, 'username' => $mediaVisit->userObj->username, 'avatar' => $this->userObj->avatar], 10);
                $media->count_fields_updated_at = array_merge(
                    $media->count_fields_updated_at,
                    ['visits' => DT::utcNow()]
                );
                $media->save();
            }
        }
    }
}
