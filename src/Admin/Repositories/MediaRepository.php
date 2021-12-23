<?php

namespace Aparlay\Core\Admin\Repositories;

use Aparlay\Core\Admin\Models\Media;
use Aparlay\Core\Models\Enums\MediaStatus;

class MediaRepository
{
    protected Media $model;

    public function __construct($model)
    {
        if (! ($model instanceof Media)) {
            throw new \InvalidArgumentException('$model should be of Media type');
        }

        $this->model = $model;
    }

    public function all($offset, $limit, $sort)
    {
        return $this->model->sortBy($sort)
            ->skip($offset)
            ->take($limit)
            ->get();
    }

    public function mediaAjax($offset, $limit, $sort)
    {
        return $this->model->sortBy($sort)
            ->skip($offset)
            ->take($limit)
            ->get();
    }

    public function countFilteredMedia($filters, $dateRangeFilter = null)
    {
        $query = $this->model->filter($filters);

        if ($dateRangeFilter) {
            $query->date($dateRangeFilter['start'], $dateRangeFilter['end']);
        }

        return $query->count();
    }

    public function getFilteredMedia($offset, $limit, $sort, $filters, $dateRangeFilter = null)
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
        $this->model->create($data);
    }

    public function update(array $data, $id)
    {
        $model = $this->model->media($id)->firstOrFail();
        $model->fill($data)->save();

        return $model->refresh();
    }

    public function delete($id)
    {
        // TODO: Implement delete() method.
    }

    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    public function getMediaStatuses()
    {
        return $this->model->getStatuses();
    }

    public function countCollection()
    {
        return $this->model->count();
    }

    public function skinScore()
    {
        return $this->model->getSkinScores();
    }

    public function awesomenessScore()
    {
        return $this->model->getAwesomenessScores();
    }

    public function pending($page)
    {
        return $this->model->completed()->recentFirst()->paginate(1, ['*'], 'page', $page);
    }
}
