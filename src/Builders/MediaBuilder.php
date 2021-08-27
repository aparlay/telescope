<?php

namespace Aparlay\Core\Builders;

use Aparlay\Core\Pagination\MediaPaginator;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Builder;

class MediaBuilder extends Builder
{
    /**
     * Paginate the given query.
     *
     * @param int|null $perPage
     * @param array $columns
     * @param string $pageName
     * @param int|null $page
     * @return MediaPaginator
     *
     * @throws \InvalidArgumentException
     */
    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        $page = $page ?: MediaPaginator::resolveCurrentPage($pageName);
        $perPage = $perPage ?: $this->model->getPerPage();
        $results = ($total = $this->toBase()->getCountForPagination())
            ? $this->forPage($page, $perPage)->get($columns)
            : $this->model->newCollection();

        return new MediaPaginator($results, $total, $perPage, $page, [
            'path'     => MediaPaginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ]);
    }
}
