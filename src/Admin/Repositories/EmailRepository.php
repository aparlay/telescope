<?php

namespace Aparlay\Core\Admin\Repositories;

use Aparlay\Core\Admin\Models\Email;
use InvalidArgumentException;

/**
 * Class EmailRepository.
 */
class EmailRepository
{
    protected Email $model;

    /**
     * EmailRepository constructor.
     *
     * @param mixed $model
     */
    public function __construct($model)
    {
        if (!($model instanceof Email)) {
            throw new InvalidArgumentException('$model should be of Email type');
        }

        $this->model = $model;
    }

    /**
     * @param mixed $offset
     * @param mixed $limit
     * @param mixed $sort
     *
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
     * @param mixed $offset
     * @param mixed $limit
     * @param mixed $sort
     *
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
     * @param mixed|null $dateRangeFilter
     * @param mixed      $filters
     *
     * @return mixed
     */
    public function countFilteredEmail($filters, $dateRangeFilter = null)
    {
        $query = $this->model->filter($filters);

        if ($dateRangeFilter) {
            $query->date($dateRangeFilter['start'], $dateRangeFilter['end']);
        }

        return $query->count();
    }

    /**
     * @param mixed|null $dateRangeFilter
     * @param mixed      $offset
     * @param mixed      $limit
     * @param mixed      $sort
     * @param mixed      $filters
     *
     * @return mixed
     */
    public function getFilteredEmail($offset, $limit, $sort, $filters, $dateRangeFilter = null)
    {
        $query = $this->model->filter($filters)
            ->sortBy($sort)
            ->skip($offset)
            ->take($limit);

        if ($dateRangeFilter) {
            $query->date($dateRangeFilter['start'], $dateRangeFilter['end']);
        }

        return $query->get();
    }

    public function create(array $data)
    {
        // TODO: Implement create() method.
    }

    public function update(array $data, $id)
    {
        // TODO: Implement create() method.
    }

    public function delete($id)
    {
        // TODO: Implement delete() method.
    }

    /**
     * @param mixed $id
     *
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

    public function countCollection()
    {
        return $this->model->count();
    }
}
