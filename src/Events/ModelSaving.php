<?php

namespace Aparlay\Core\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MongoDB\BSON\ObjectId;

class ModelSaving
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($model)
    {
        if (! empty($model->created_by) && is_string($model->created_by)) {
            $model->created_by = new ObjectId($model->created_by);
        }

        if (! empty($model->updated_by) && is_string($model->updated_by)) {
            $model->updated_by = new ObjectId($model->updated_by);
        }

        if (! empty($model->user_id) && is_string($model->user_id)) {
            $model->user_id = new ObjectId($model->user_id);
        }

        if (! empty($model->media_id) && is_string($model->media_id)) {
            $model->media_id = new ObjectId($model->media_id);
        }
    }
}
