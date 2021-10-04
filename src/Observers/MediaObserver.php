<?php

namespace Aparlay\Core\Observers;

use Aparlay\Core\Api\V1\Services\MediaService;
use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Jobs\DeleteMediaLike;
use Aparlay\Core\Models\Media;
use Exception;
use MongoDB\BSON\ObjectId;

class MediaObserver extends BaseModelObserver
{
    /**
     * Create a new event instance.
     *
     * @param Media $media
     * @return void
     * @throws Exception
     */
    public function created(Media $media)
    {
        $creatorUser = $media->userObj;

        if ($media->isDirty('status')) {
            $creatorUserMedias = $creatorUser->medias;
            if (! empty($creatorUserMedias)) {
                foreach ($creatorUserMedias as $creatorUserMedia) {
                    if ((string) $creatorUserMedia['_id'] === (string) $media->_id) {
                        $creatorUser->removeFromSet('medias', $creatorUserMedia);
                    }
                }
            }

            $creatorUser->media_count = Media::creator($media->created_by)->count();
            $medias = [];
            $completedMedias = Media::creator($media->created_by)->completed()->recentFirst()->limit(30)->get();
            if (! $completedMedias->isEmpty()) {
                foreach ($completedMedias as $completedMedia) {
                    $basename = basename(
                        $completedMedia['file'],
                        '.'.pathinfo($completedMedia['file'], PATHINFO_EXTENSION)
                    );
                    $file = config('app.cdn.videos').$completedMedia['file'];
                    $cover = config('app.cdn.covers').$basename.'.jpg';
                    $medias[] = [
                        '_id' => new ObjectId($completedMedia['_id']), 'file' => $file, 'cover' => $cover,
                        'status' => $completedMedia['status'],
                    ];
                }
            }
            $creatorUser->medias = $medias;
            $creatorUser->count_fields_updated_at = array_merge(
                $creatorUser->count_fields_updated_at,
                ['medias' => DT::utcNow()]
            );
            $creatorUser->save();
        }
        //dispatch((new UploadMedia((string) $media->userObj->_id, (string) $media->_id, $media->file))->onQueue('low'));
    }

    /**
     * Create a new event instance.
     *
     * @param Media $model
     * @return void
     * @throws Exception
     */
    public function saving($model): void
    {
        parent::saving($model);
        $model->description = (string) $model->description;
        $model->hashtags = MediaService::extractHashtags($model->description);

        $extractedPeople = MediaService::extractPeople($model->description);
        if (! empty($extractedPeople)) {
            $users = [];
            $usersQuery = \Aparlay\Core\Models\User::select([
                'username', 'avatar', '_id',
            ])->usernames($extractedPeople)->limit(20)->get();
            if (! $usersQuery->isEmpty()) {
                foreach ($usersQuery->toArray() as $user) {
                    $users[] = $model->createSimpleUser($user);
                }
            }
            $model->people = $users;
        }

        if ($model->wasChanged('file') && str_contains($model->file, config('app.cdn.videos'))) {
            $model->file = str_replace(config('app.cdn.videos'), '', $model->file);
        }

        if ($model->status === Media::STATUS_DENIED) {
            $model->visibility = Media::VISIBILITY_PRIVATE;
        }

        if ($model->wasRecentlyCreated) {
            $model->slug = MediaService::generateSlug(6);
        }
    }

    /**
     * Create a new event instance.
     *
     * @param Media $media
     * @return void
     * @throws Exception
     */
    public function saved(Media $media): void
    {
        if ($media->wasRecentlyCreated === false) {
            $creatorUser = $media->userObj;

            if ($media->status === Media::STATUS_USER_DELETED && $media->isDirty('status')) {
                $creatorUser->media_count--;

                $file = config('app.cdn.videos').$media->file;
                $cover = config('app.cdn.covers').$media->filename.'.jpg';
                $creatorUser->removeFromSet(
                    'medias',
                    ['_id' => $media->_id, 'file' => $file, 'cover' => $cover, 'status' => $media->status]
                );
                $creatorUser->count_fields_updated_at = array_merge(
                    $creatorUser->count_fields_updated_at,
                    ['medias' => DT::utcNow()]
                );
                $creatorUser->save();

                dispatch((new DeleteMediaLike((string) $media->_id, (string) $creatorUser->_id))->onQueue('low'));
            }
        }
    }

    /**
     * Create a new event instance.
     *
     * @param Media $media
     * @return void
     * @throws Exception
     */
    public function deleted(Media $media): void
    {
        $creatorUser = $media->userObj;
        $creatorUser->media_count--;

        $file = config('app.cdn.videos').$media->file;
        $cover = config('app.cdn.covers').$media->filename.'.jpg';
        $creatorUser->removeFromSet('medias', ['_id' => $media->_id, 'file' => $file, 'cover' => $cover, 'status' => $media->status]);
        $creatorUser->count_fields_updated_at = array_merge(
            $creatorUser->count_fields_updated_at,
            ['medias' => DT::utcNow()]
        );
        $creatorUser->save();

        dispatch((new DeleteMediaLike((string) $media->_id, (string) $creatorUser->_id))->onQueue('low'));
    }
}
