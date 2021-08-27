<?php

namespace Aparlay\Core\Builders;

use Aparlay\Core\Pagination\CorePaginator;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Builder;

class BaseBuilder extends Builder
{
    /**
     * Paginate the given query.
     *
     * @param int|null $perPage
     * @param array $columns
     * @param string $pageName
     * @param int|null $page
     * @return CorePaginator
     *
     * @throws \InvalidArgumentException
     */
    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        $page = $page ?: CorePaginator::resolveCurrentPage($pageName);
        $perPage = $perPage ?: $this->model->getPerPage();
        $results = ($total = $this->toBase()->getCountForPagination())
            ? $this->forPage($page, $perPage)->get($columns)
            : $this->model->newCollection();

        return new CorePaginator($results, $total, $perPage, $page, [
            'path'     => CorePaginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ]);
    }
}
