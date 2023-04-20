<?php

namespace Aparlay\Core\Observers;

use Aparlay\Core\Models\BaseModel;
use Illuminate\Support\Facades\Auth;
use MongoDB\BSON\ObjectId;

class BaseModelObserver
{
    /**
     * Create a new event instance.
     *
     * @param BaseModel $model
     */
    public function creating($model): void
    {
        $loggedInUser      = Auth::user();
        $model->created_by = !is_null($loggedInUser) ? new ObjectId($loggedInUser->_id) : $model->created_by;
        $model->updated_by = !is_null($loggedInUser) ? new ObjectId($loggedInUser->_id) : $model->updated_by;
    }

    /**
     * Create a new event instance.
     *
     * @param BaseModel $model
     */
    public function updating($model): void
    {
        $loggedInUser      = Auth::user();
        $model->updated_by = !is_null($loggedInUser) ? new ObjectId($loggedInUser->_id) : $model->updated_by;
    }

    /**
     * Create a new event instance.
     *
     * @param BaseModel $model
     */
    public function saving($model): void
    {
        if (!empty($model->created_by) && is_string($model->created_by)) {
            $model->created_by = new ObjectId($model->created_by);
        }

        if (!empty($model->updated_by) && is_string($model->updated_by)) {
            $model->updated_by = new ObjectId($model->updated_by);
        }

        if (!empty($model->user_id) && is_string($model->user_id)) {
            $model->user_id = new ObjectId($model->user_id);
        }

        if (!empty($model->media_id) && is_string($model->media_id)) {
            $model->media_id = new ObjectId($model->media_id);
        }
    }
}
