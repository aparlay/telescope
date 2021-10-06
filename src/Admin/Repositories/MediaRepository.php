<?php

namespace Aparlay\Core\Admin\Repositories;

use Aparlay\Core\Admin\Models\Media;
use Aparlay\Core\Helpers\Cdn;
use Illuminate\Support\Collection;

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
        // TODO: Implement all() method.
    }

    public function create(array $data)
    {
        // TODO: Implement create() method.
    }

    public function update(array $data, $id)
    {
        // TODO: Implement update() method.
    }

    public function delete($id)
    {
        // TODO: Implement delete() method.
    }

    public function find($id)
    {
        // TODO: Implement find() method.
    }

    public function getList(array $options = [])
    {
        $onPage = ! empty($options['onPage']) ? $options['onPage'] : 20;

        $mediaCollection = $this->model->paginate($onPage);

        foreach ($mediaCollection as $collect) {
            $collect->status_text = $this->model->getStatus_color($collect->status);
            $collect->cover = Cdn::cover(! empty($value['file']) ? $value['file'].'.jpg' : 'default.jpg');
        }

        return $mediaCollection;
    }
}
