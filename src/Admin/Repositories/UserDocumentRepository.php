<?php

namespace Aparlay\Core\Admin\Repositories;

use Aparlay\Core\Api\V1\Models\UserDocument;

class UserDocumentRepository
{
    protected static $model = UserDocument::class;

    /**
     * @param $offset
     * @param $limit
     * @param $sort
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
     * @param $offset
     * @param $limit
     * @param $sort
     * @param $filters
     * @return mixed
     */
    public function getFiltered($offset, $limit, $sort, $filters)
    {
        $query = static::$model::filter($filters)
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
