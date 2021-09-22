<?php

namespace Aparlay\Core\Observers;

use Aparlay\Core\Models\BaseModel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MongoDB\BSON\ObjectId;

class BaseModelObserver
{
    /**
     * Create a new event instance.
     *
     * @param  BaseModel  $model
     * @return void
     */
    public function saving($model): void
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

        if (!empty($model->casts)) {
            foreach ($model->casts as $field => $type) {
                $value = match ($type) {
                    'array' => (array) $model->$field,
                    'boolean' => (boolean) $model->$field,
                    'integer' => (integer) $model->$field,
                    'float' => (float) $model->$field,
                    'string' => (string) $model->$field,
                    default => $model->$field,
                };

                $model->$field = $value;
            }
        }

    }
}
