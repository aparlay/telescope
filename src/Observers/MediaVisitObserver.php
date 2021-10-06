<?php

namespace Aparlay\Core\Observers;

use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Models\MediaVisit;
use Exception;

class MediaVisitObserver extends BaseModelObserver
{
    /**
     * Create a new event instance.
     *
     * @param MediaVisit $model
     * @return void
     * @throws Exception
     */
    public function saving($model): void
    {
        $model->addToSet('media_ids', $model->media_id);
        parent::saving($model);
    }

    /**
     * Create a new event instance.
     *
     * @param  MediaVisit  $mediaVisit
     * @return void
     */
    public function saved(MediaVisit $mediaVisit): void
    {
        if ($mediaVisit->wasRecentlyCreated || $mediaVisit->wasChanged('media_ids')) {
            $media = $mediaVisit->mediaObj;

            if ($mediaVisit->duration > ($media->length / 4)) {
                if ($mediaVisit->duration <= $media->length) {
                    $media->length_watched += $mediaVisit->duration;
                }
                $media->visit_count++;
                $media->addToSet('visits', [
                    '_id' => $mediaVisit->userObj->_id,
                    'username' => $mediaVisit->userObj->username,
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
