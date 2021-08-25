<?php

namespace Aparlay\Core\Builders;

use Aparlay\Core\Pagination\CorePaginator;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Builder;

class BaseBuilder extends Builder
{
    /**
     * Create a new paginator instance.
     *
     * @param \Illuminate\Support\Collection $items
     * @param int $total
     * @param int $perPage
     * @param int $currentPage
     * @param array $options
     * @return CorePaginator
     */
//    protected function paginator($items, $total, $perPage, $currentPage, $options)
//    {
//        return Container::getInstance()->makeWith(
//            CorePaginator::class,
//            compact(
//                'items',
//                'total',
//                'perPage',
//                'currentPage',
//                'options'
//            )
//        );
//    }

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
