<?php

namespace Aparlay\Core\Observers;

use Aparlay\Core\Events\ModelSaving;
use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Models\MediaVisit;
use Exception;

class MediaVisitObserver
{
    /**
     * Create a new event instance.
     *
     * @return void
     * @throws Exception
     */
    public function saving(MediaVisit $mediaVisit)
    {
        ModelSaving::dispatch($mediaVisit);
        $mediaVisit->push('media_ids', $mediaVisit->media_id, true);
    }


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function saved(MediaVisit $mediaVisit)
    {
        if ($mediaVisit->wasChanged('media_ids')) {
            $media = $mediaVisit->mediaObj;

            if ($mediaVisit->duration > ($media->length / 4)) {
                if ($mediaVisit->duration <= $media->length) {
                    $media->length_watched += $mediaVisit->duration;
                }
                $media->visit_count++;
                $media->addToSet('visits', [
                    '_id' => $mediaVisit->userObj->_id, 'username' => $mediaVisit->userObj->username,
                    'avatar' => $mediaVisit->userObj->avatar,
                ], 10);
                $media->count_fields_updated_at = array_merge(
                    $media->count_fields_updated_at,
                    ['visits' => DT::utcNow()]
                );
                $media->save();
            }
        }
    }
}
