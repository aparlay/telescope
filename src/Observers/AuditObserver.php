<?php

namespace Aparlay\Core\Observers;

use Aparlay\Core\Models\Audit;
use Exception;
use MongoDB\BSON\ObjectId;

class AuditObserver
{
    /**
     * Handle the User "creating" event.
     *
     * @param Audit $model
     *
     * @throws Exception
     */
    public function saving($model): void
    {
        if (!empty($model->user_id) && is_string($model->user_id)) {
            $model->user_id = new ObjectId($model->user_id);
        }
        if (!empty($model->auditable_id) && is_string($model->auditable_id)) {
            $model->auditable_id = new ObjectId($model->auditable_id);
        }
    }
}
