<?php

namespace Aparlay\Core\Admin\Services;

use Aparlay\Core\Admin\Models\Media;
use Aparlay\Core\Admin\Models\User;
use Aparlay\Core\Admin\Repositories\MediaRepository;
use Aparlay\Core\Admin\Repositories\UserRepository;
use Aparlay\Core\Helpers\ActionButtonBladeComponent;
use Aparlay\Core\Helpers\Cdn;
use Aparlay\Core\Jobs\UploadMedia;
use Aparlay\Core\Models\Enums\MediaStatus;
use MongoDB\BSON\ObjectId;

class MediaService extends AdminBaseService
{
    protected MediaRepository $mediaRepository;
    protected UserRepository $userRepository;

    public function __construct()
    {
        $this->mediaRepository = new MediaRepository(new Media());
        $this->userRepository = new UserRepository(new User());
        $this->filterableField = ['creator.username', 'status', 'created_at'];
        $this->sorterableField = ['creator.username', 'description', 'status', 'like_count', 'sort_score', 'visit_count', 'created_at'];
    }



    public function firstCompleted()
    {
        $media = $this->mediaRepository->firstCompleted();

        if ($media) {
            $media->status = MediaStatus::IN_REVIEW->value;
            $media->save();
            return $media;
        }
        return null;
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
        $dateRangeFilter = null;

        if (! empty($filters)) {
            if (isset($filters['created_at'])) {
                $dateRangeFilter = $this->getDateRangeFilter($filters['created_at']);
                unset($filters['created_at']);
            }

            $medias = $this->mediaRepository->getFilteredMedia($offset, $limit, $sort, $filters, $dateRangeFilter);
        } else {
            $medias = $this->mediaRepository->mediaAjax($offset, $limit, $sort);
        }

        $this->appendAttributes($medias, $filters, $dateRangeFilter);

        return $medias;
    }

    /**
     * @param $medias
     * @param $filters
     */
    public function appendAttributes($medias, $filters, $dateRangeFilter)
    {
        $medias->total_media = $this->mediaRepository->countCollection();
        $medias->total_filtered_media = ! empty($filters) || $dateRangeFilter ? $this->mediaRepository->countFilteredMedia($filters, $dateRangeFilter) : $medias->total_media;

        foreach ($medias as $media) {
            $media->file = '<img src="'.Cdn::cover(! empty($media->file) ? str_replace('.mp4', '', $media->file).'.jpg?width=100' : 'default.jpg?width=100').'"/>';
            $media->sort_score = $media->sort_score ? round($media->sort_score) : ActionButtonBladeComponent::defaultValueNotSet();
            $media->status_badge = ActionButtonBladeComponent::getBadge($media->status_color, $media->status_name);
            $media->action = ActionButtonBladeComponent::getViewActionButton($media->_id, 'media');
            $media->date_formatted = $media->created_at->toDateTimeString();
            $media->like_count = $media->like_count ?? ActionButtonBladeComponent::defaultValueNotSet();
            $media->visit_count = $media->visit_count ?? ActionButtonBladeComponent::defaultValueNotSet();
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
        $media->round_length = round($media->length);

        return $media;
    }

    /**
     * @param $order
     */
    public function pending($page)
    {
        return $this->mediaRepository->pending($page);
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

    public function reupload($media)
    {
        $this->mediaRepository->update(['file' => request()->input('file')], $media->_id);
        UploadMedia::dispatch($media->creator['_id'], $media->_id, request()->input('file'))->onQueue('low');
    }

    public function generateSlug($length)
    {
        $slug = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, $length);

        return (null === Media::slug($slug)->first()) ? $slug : $this->generateSlug($length);
    }

    public function upload()
    {
        $user = $this->userRepository->find(request()->input('user_id'));
        $data = [
            'user_id' => new ObjectId(request()->input('user_id')),
            'file' => request()->input('file'),
            'description' => request()->input('description', ''),
            'slug' => self::generateSlug(6),
            'count_fields_updated_at' => [],
            'visibility' => $user->visibility,
            'status' => MediaStatus::QUEUED->value,
            'creator' => [
                '_id'      => new ObjectId($user->_id),
                'username' => $user->username,
                'avatar'   => $user->avatar,
            ],
        ];

        $this->mediaRepository->create($data);
    }

    public function countCollection()
    {
        return $this->mediaRepository->countCollection();
    }

    public function countCompleted()
    {
        return $this->mediaRepository->countCompleted();
    }
}
