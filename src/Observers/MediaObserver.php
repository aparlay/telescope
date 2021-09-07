<?php

namespace Aparlay\Core\Observers;

use Aparlay\Core\Api\V1\Services\MediaService;
use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Jobs\DeleteMediaLike;
use Aparlay\Core\Models\Media;
use Exception;
use MongoDB\BSON\ObjectId;

class MediaObserver
{
    /**
     * Create a new event instance.
     *
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
     * @return void
     * @throws Exception
     */
    public function saving(Media $media)
    {
        $media->hashtags = MediaService::extractHashtags($media->description);

        $extractedPeople = MediaService::extractPeople($media->description);
        if (! empty($extractedPeople)) {
            $users = [];
            $usersQuery = \Aparlay\Core\Models\User::select([
                'username', 'avatar', '_id',
            ])->usernames($extractedPeople)->limit(20)->get();
            if (! $usersQuery->isEmpty()) {
                foreach ($usersQuery->toArray() as $user) {
                    $users[] = $media->createSimpleUser($user);
                }
            }
            $media->people = $users;
        }

        if ($media->wasChanged('file') && str_contains($media->file, config('app.cdn.videos'))) {
            $media->file = str_replace(config('app.cdn.videos'), '', $media->file);
        }

        if ($media->status === Media::STATUS_DENIED) {
            $media->visibility = Media::VISIBILITY_PRIVATE;
        }

        if ($media->wasRecentlyCreated) {
            $media->slug = MediaService::generateSlug(6);
        }
    }

    /**
     * Create a new event instance.
     *
     * @return void
     * @throws Exception
     */
    public function saved(Media $media)
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
     * @return void
     * @throws Exception
     */
    public function deleted(Media $media)
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
