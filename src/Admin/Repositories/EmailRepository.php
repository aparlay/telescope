<?php

namespace Aparlay\Core\Admin\Repositories;

use Aparlay\Core\Admin\Models\Email;

/**
 * Class EmailRepository.
 */
class EmailRepository
{
    /**
     * @var Email
     */
    protected Email $model;

    /**
     * EmailRepository constructor.
     * @param $model
     */
    public function __construct($model)
    {
        if (! ($model instanceof Email)) {
            throw new \InvalidArgumentException('$model should be of Email type');
        }

        $this->model = $model;
    }

    /**
     * @param $offset
     * @param $limit
     * @param $sort
     * @return mixed
     */
    public function all($offset, $limit, $sort)
    {
        return $this->model
            ->sortBy($sort)
            ->skip($offset)
            ->take($limit)
            ->get();
    }

    /**
     * @param $offset
     * @param $limit
     * @param $sort
     * @return mixed
     */
    public function emailAjax($offset, $limit, $sort)
    {
        return $this->model
            ->sortBy($sort)
            ->skip($offset)
            ->take($limit)
            ->get();
    }

    /**
     * @param $filters
     * @return mixed
     */
    public function countFilteredEmail($filters)
    {
        return $this->model->filter($filters)->count();
    }

    /**
     * @param $offset
     * @param $limit
     * @param $sort
     * @param $filters
     * @return mixed
     */
    public function getFilteredEmail($offset, $limit, $sort, $filters)
    {
        return $this->model->filter($filters)
            ->sortBy($sort)
            ->skip($offset)
            ->take($limit)
            ->get();
    }

    /**
     * @param array $data
     */
    public function create(array $data)
    {
        // TODO: Implement create() method.
    }

    /**
     * @param array $data
     * @param $id
     */
    public function update(array $data, $id)
    {
        // TODO: Implement create() method.
    }

    /**
     * @param $id
     */
    public function delete($id)
    {
        // TODO: Implement delete() method.
    }

    /**
     * @param $id
     * @return mixed
     */
    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    /**
     * @return array
     */
    public function getEmailStatuses()
    {
        return $this->model->getStatuses();
    }

    /**
     * @return array
     */
    public function getEmailTypes()
    {
        return $this->model->getTypes();
    }

}
