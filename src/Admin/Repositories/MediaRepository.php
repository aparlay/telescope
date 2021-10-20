<?php

namespace Aparlay\Core\Admin\Repositories;

use Aparlay\Core\Admin\Models\Media;

class MediaRepository implements RepositoryInterface
{
    protected Media $model;

    public function __construct($model)
    {
        if (! ($model instanceof Media)) {
            throw new \InvalidArgumentException('$model should be of Media type');
        }

        $this->model = $model;
    }

    public function all()
    {
        return $this->model->orderBy('created_at', 'desc')->paginate(20);
    }

    public function create(array $data)
    {
        // TODO: Implement create() method.
    }

    public function update(array $data, $id)
    {
        $model = $this->model->media($id)->firstOrFail();
        $model->update($data);
        $model->refresh();
    }

    public function delete($id)
    {
        // TODO: Implement delete() method.
    }

    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    public function skinScore()
    {
        return $this->model->getSkinScores();
    }

    public function awesomenessScore()
    {
        return $this->model->getAwesomenessScores();
    }
}
