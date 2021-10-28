<?php

namespace Aparlay\Core\Admin\Observers;

use Aparlay\Core\Admin\Models\Alert;
use Aparlay\Core\Observers\BaseModelObserver;
use MongoDB\BSON\ObjectId;

class AlertObserver extends BaseModelObserver
{
    /**
     * Handle the Follow "creating" event.
     *
     * @param  Alert  $model
     * @return void
     */
    public function creating($model): void
    {
        $model->user_id = new ObjectId($model->user_id);
        $model->media_id = $model->media_id ? new ObjectId($model->media_id) : null;
    }
}
