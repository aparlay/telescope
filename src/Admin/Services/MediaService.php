<?php

namespace Aparlay\Core\Admin\Services;

use Aparlay\Core\Admin\Models\Media;
use Aparlay\Core\Admin\Models\User;
use Aparlay\Core\Admin\Repositories\MediaRepository;
use Aparlay\Core\Admin\Repositories\UserRepository;
use Aparlay\Core\Helpers\ActionButtonBladeComponent;
use Aparlay\Core\Helpers\Cdn;
use Aparlay\Core\Helpers\DT;
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

    /**
     * @return bool
     */
    public function isModerationQueueNotEmpty(): bool
    {
        return $this->mediaRepository->countCompleted() > 0;
    }

    public function nextItemToReview($currentUser, $mediaId)
    {
        $mediaItem = $this->mediaRepository->nextItemToReview($mediaId);

        if ($mediaItem) {
            $this->mediaRepository->revertAllToCompleted($currentUser, $mediaItem);
            $mediaItem = $this->mediaRepository->setToUnderReview($mediaItem);
        }

        return $mediaItem;
    }

    public function prevItemToReview($currentUser, $mediaId)
    {
        $itemToReview = $this->mediaRepository->prevItemToReview($mediaId);

        if ($itemToReview) {
            $this->mediaRepository->revertAllToCompleted($currentUser);
            $itemToReview = $this->mediaRepository->setToUnderReview($itemToReview);
        }

        return $itemToReview;
    }

    public function hasNextItemToReview($mediaId): bool
    {
        return ! empty($this->mediaRepository->nextItemToReview($mediaId));
    }

    /**
     * @param $userId
     * @return bool
     */
    public function hasPrevItemToReview($mediaId): bool
    {
        return ! empty($this->mediaRepository->prevItemToReview($mediaId));
    }

    public function firstItemToReview($currentAdminUser)
    {
        $underReviewExists = $this->mediaRepository->firstUnderReview($currentAdminUser);

        if ($underReviewExists) {
            return $underReviewExists;
        }

        $pendingMedia = $this->mediaRepository->firstToReview();

        if ($pendingMedia) {
            $this->mediaRepository->revertAllToCompleted($currentAdminUser);
            $pendingMedia = $this->mediaRepository->setToUnderReview($pendingMedia);
        }

        return $pendingMedia;
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
            'is_protected',
            'is_music_licensed',
        ]);

        $dataModified = [
            'visibility' => request()->boolean('visibility'),
            'is_protected' => request()->boolean('is_protected'),
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

    public function calculateSortScore($media)
    {
        $media->sort_score = $media->awesomeness_score;
        $media->sort_score += ($media->time_score / 2);
        $media->sort_score += ($media->like_score / 3);
        $media->sort_score += ($media->visit_score / 3);

        $media->save();

        return $media;
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
            'count_fields_updated_at' => [
                'followers' => DT::utcNow(),
                'followings' => DT::utcNow(),
                'blocks' => DT::utcNow(),
                'likes' => DT::utcNow(),
                'medias' => DT::utcNow(),
                'followed_hashtags' => DT::utcNow(),
            ],
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
