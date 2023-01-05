<?php

namespace Aparlay\Core\Admin\Repositories;

use Aparlay\Core\Admin\Models\Media;
use Aparlay\Core\Admin\Models\User;
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

    /**
     * @return int
     */
    public function countToReview()
    {
        return Media::query()
            ->whereIn('status', [MediaStatus::COMPLETED->value, MediaStatus::IN_REVIEW->value])
            ->count();
    }

    public function firstToReview()
    {
        return Media::query()
            ->completed()
            ->first();
    }

    /**
     * @param $user
     * @return mixed
     */
    public function firstUnderReview($currentAdminUser)
    {
        return Media::query()
            ->inReview()
            ->updatedBy($currentAdminUser->_id)
            ->first();
    }

    /**
     * @param $currentUser
     * @param $exeptMedia
     * @return mixed
     */
    public function revertAllToCompleted($currentUser)
    {
        return Media::query()
            ->inReview()
            ->updatedBy($currentUser->_id)
            ->update(['status' => MediaStatus::COMPLETED->value]);
    }

    /**
     * @param $currentItemId
     * @return mixed
     */
    public function prevItemToReview($currentItemId)
    {
        return Media::query()
            ->completed()
            ->where('_id', '<', $currentItemId)
            ->orderBy('_id', 'DESC')
            ->first();
    }

    /**
     * @param $currentItemId
     * @return mixed
     */
    public function nextItemToReview($currentItemId)
    {
        return Media::query()
            ->completed()
            ->where('_id', '>', $currentItemId)
            ->orderBy('_id', 'ASC')
            ->first();
    }

    /**
     * @param $media
     * @return mixed
     */
    public function setToUnderReview($media)
    {
        $media->status = MediaStatus::IN_REVIEW->value;
        $media->save();

        return $media;
    }

    public function firstPending()
    {
        return Media::query()->completed()->latest()->first();
    }

    public function all($offset, $limit, $sort)
    {
        return $this->model->sortBy($sort)
            ->skip($offset)
            ->take($limit)
            ->options(['allowDiskUse' => true])
            ->get();
    }

    public function mediaAjax($offset, $limit, $sort)
    {
        return $this->model->sortBy($sort)
            ->skip($offset)
            ->take($limit)
            ->options(['allowDiskUse' => true])
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

    public function updateVisibility(User $creator, int $visibility)
    {
        return Media::query()
            ->creator($creator->_id)
            ->update(['visibility' => $visibility]);
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

    public function beautyScore()
    {
        return $this->model->getBeautyScores();
    }

    public function pending($page)
    {
        return $this->model->completed()->recentFirst()->paginate(1, ['*'], 'page', $page);
    }

    public function countCompleted()
    {
        return $this->model->completed()->count();
    }
}
