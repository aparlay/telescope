<?php

namespace Aparlay\Core\Admin\Repositories;

use Aparlay\Core\Admin\Models\Note;

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

    public function create(array $data)
    {
        return $this->model->create($data);
    }
}
