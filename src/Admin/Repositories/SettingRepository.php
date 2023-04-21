<?php

namespace Aparlay\Core\Admin\Repositories;

use Aparlay\Core\Admin\Models\Setting;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class SettingRepository
{
    protected Setting $model;

    public function __construct($model)
    {
        if (!($model instanceof Setting)) {
            throw new InvalidArgumentException('$model should be of Setting type');
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

    public function countFilteredSettings($filters, $dateRangeFilter = null)
    {
        $query = $this->model->filter($filters);

        if ($dateRangeFilter) {
            $query->date($dateRangeFilter['start'], $dateRangeFilter['end']);
        }

        return $query->count();
    }

    public function getFilteredSettings($offset, $limit, $sort, $filters, $dateRangeFilter = null)
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

    public function getSettingGroup()
    {
        return $this->model->settingGroup();
    }

    /**
     * @param mixed $id
     *
     * @return Setting|Setting[]|Collection|Model
     */
    public function find($id): Model|Collection|Setting|array
    {
        return $this->model->findOrFail($id);
    }

    public function countCollection(): int
    {
        return $this->model->count();
    }

    public function update($data, $id): bool
    {
        return $this->find($id)->fill($data)->save();
    }

    public function findSettingByTitleByGroup($title, $group)
    {
        return $this->model->title($title)->group($group)->first();
    }

    public function store($data): Model|Setting
    {
        return $this->model->create($data);
    }

    public function delete($id): ?bool
    {
        return $this->model->findOrFail($id)->delete();
    }
}
