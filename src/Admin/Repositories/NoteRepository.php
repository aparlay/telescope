<?php

namespace Aparlay\Core\Admin\Repositories;

use Aparlay\Core\Admin\Models\Note;
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

    public function store(array $data)
    {
        $creator = auth()->user();
        $data['creator'] = [
            '_id' => new ObjectId($creator->_id),
            'username' => $creator->username,
            'avatar' => $creator->avatar,
        ];

        return $this->model->create($data);
    }

    public function delete($id): ?bool
    {
        return $this->model->findOrFail($id)->delete();
    }
}
