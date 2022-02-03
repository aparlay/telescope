<?php

namespace Aparlay\Core\Api\V1\Repositories;

use Aparlay\Core\Api\V1\Models\Note;
use MongoDB\BSON\ObjectId;

class NoteRepository
{
    protected Note $model;

    public function __construct($model)
    {
        if (! ($model instanceof Note)) {
            throw new \InvalidArgumentException('$model should be of Note type');
        }

        $this->model = $model;
    }
}
