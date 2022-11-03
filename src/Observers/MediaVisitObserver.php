<?php

namespace Aparlay\Core\Observers;

use Aparlay\Core\Models\MediaVisit;
use Exception;
use Psr\SimpleCache\InvalidArgumentException;

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
}
