<?php

namespace Aparlay\Core\Admin\Services;

use Aparlay\Core\Admin\Models\Media;
use Aparlay\Core\Admin\Repositories\MediaRepository;
use Aparlay\Core\Helpers\Cdn;

class MediaService extends AdminBaseService
{
    protected MediaRepository $MediaRepository;

    public function __construct()
    {
        $this->userRepository = new MediaRepository(new Media());

        $this->filterableField = ['creator.username', 'status'];
        $this->sorterableField = ['creator.username', 'status', 'created_at'];
    }

    /**
     * @return mixed
     */
    public function getFilteredMedia(): mixed
    {
        $offset = (int) request()->get('start');
        $limit = (int) request()->get('length');

        $filters = $this->getFilters();
        $sort = $this->tableSort();
        if (! empty($filters)) {
            $medias = $this->mediaRepository->getFilteredMediaAjax($offset, $limit, $sort, $filters);
        } else {
            $medias = $this->mediaRepository->mediaAjax($offset, $limit, $sort);
        }

        $this->appendAttributes($medias, $filters);

        return $medias;
    }

    /**
     * @param $medias
     * @param $filters
     */
    public function appendAttributes($medias, $filters)
    {
        $medias->total_medias = $this->mediaRepository->countCollection();
        $medias->total_filtered_medias = ! empty($filters) ? $this->mediaRepository->countFilteredUserAjax($filters) : $medias->total_users;

        foreach ($medias as $media) {
            $media->status_badge = [
                'status' => $this->createBadge($media->status_color, $media->status),
            ];
        }
    }

    /**
     * @param $id
     */
    public function find($id)
    {
        $user = $this->userRepository->find($id);

        $statusBadge = [
            'status' => $user->status_name,
            'color' => $user->status_color,
        ];

        $user->status_badge = $statusBadge;

        return $user;
    }

    /**
     * @return array
     */
    public function getMediaStatuses(): array
    {
        return $this->userRepository->getMediaStatuses();
    }

    /**
     * @return array
     */
    public function getVisibilities(): array
    {
        return $this->userRepository->getVisibilities();
    }
}
