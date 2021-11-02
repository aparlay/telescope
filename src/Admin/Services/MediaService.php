<?php

namespace Aparlay\Core\Admin\Services;

use Aparlay\Core\Admin\Models\Media;
use Aparlay\Core\Admin\Repositories\MediaRepository;
use Aparlay\Core\Helpers\ActionButtonBladeComponent;
use Aparlay\Core\Helpers\Cdn;
use Illuminate\Http\Request;

class MediaService extends AdminBaseService
{
    protected MediaRepository $mediaRepository;

    public function __construct()
    {
        $this->mediaRepository = new MediaRepository(new Media());

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
            $medias = $this->mediaRepository->getFilteredMedia($offset, $limit, $sort, $filters);
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
        $medias->total_media = $this->mediaRepository->countCollection();
        $medias->total_filtered_media = ! empty($filters) ? $this->mediaRepository->countFilteredMedia($filters) : $medias->total_media;

        foreach ($medias as $media) {
            $media->file = '<img src="'.Cdn::cover(! empty($media->file) ? str_replace('.mp4', '', $media->file).'.jpg?width=100' : 'default.jpg?width=100').'"/>';
            $media->sort_score = $media->sort_score ?? '';
            $media->status_badge = ActionButtonBladeComponent::getBadge($media->status_color, $media->status_name);
            $media->action = ActionButtonBladeComponent::getViewActionButton($media->_id, 'media');
        }
    }

    /**
     * @param $id
     */
    public function find($id)
    {
        $media = $this->mediaRepository->find($id);
        $statusBadge = [
            'status' => $media->status_name,
            'color' => $media->status_color,
        ];

        $media->status_badge = $statusBadge;

        return $media;
    }

    /**
     * @param $order
     */
    public function pending($order)
    {
        return $this->mediaRepository->pendingMedia($order);
    }

    /**
     * @return array
     */
    public function getMediaStatuses(): array
    {
        return $this->mediaRepository->getMediaStatuses();
    }

    /**
     * @return array
     */
    public function getVisibilities(): array
    {
        return $this->userRepository->getVisibilities();
    }

    /**
     * @param $id
     */
    public function update($id)
    {
        $data = request()->only([
            'description',
            'status',
            'skin_score',
            'visibility',
            'is_music_licensed',
        ]);

        $dataModified = [
            'visibility' => request()->boolean('visibility'),
            'is_music_licensed' => request()->boolean('is_music_licensed'),
            'scores' => [
                [
                    'type' => 'skin',
                    'score' => request()->skin_score,
                ],
                [
                    'type' => 'awesomeness',
                    'score' => request()->awesomeness_score,
                ],
            ],
        ];

        $data = array_merge($data, $dataModified);

        return $this->mediaRepository->update($data, $id);
    }
}
