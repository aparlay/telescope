<?php

namespace Aparlay\Core\Admin\Repositories;

class AbstractBaseRepository
{
    protected static $model;

    /**
     * @return mixed
     */
    public function all($offset, $limit, $sort)
    {
        return static::$model::sortBy($sort)
            ->skip($offset)
            ->take($limit)
            ->get();
    }

    /**
     * @return mixed
     */
    public function getFiltered($offset, $limit, $sort, $filters)
    {
        $query  = static::$model::filter($filters)
            ->sortBy($sort)
            ->skip($offset)
            ->take($limit);

        $result = $query->get();

        return $result;
    }

    public function countAll()
    {
        return static::$model::count();
    }

    public function countFiltered($filters)
    {
        return static::$model::filter($filters)->count();
    }
}
