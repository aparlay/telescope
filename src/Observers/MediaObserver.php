<?php

namespace Aparlay\Core\Observers;

use Aparlay\Core\Api\V1\Notifications\UserDeleteMedia;
use Aparlay\Core\Api\V1\Services\MediaService;
use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Jobs\DeleteMediaComments;
use Aparlay\Core\Jobs\DeleteMediaLikes;
use Aparlay\Core\Jobs\DeleteMediaUserNotifications;
use Aparlay\Core\Jobs\RecalculateHashtag;
use Aparlay\Core\Jobs\UploadMedia;
use Aparlay\Core\Models\Enums\MediaStatus;
use Aparlay\Core\Models\Enums\MediaVisibility;
use Aparlay\Core\Models\Hashtag;
use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\User;
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
    public function created($media)
    {
        $creatorUser = $media->userObj;

        if ($media->isDirty('status')) {
            $creatorUser->media_count = Media::query()->creator($creatorUser->_id)->count();
            $medias = [];
            foreach (Media::query()->creator($creatorUser->_id)->completed()->recentFirst()->limit(30)->get() as $completedMedia) {
                $basename = basename(
                    $completedMedia['file'],
                    '.'.pathinfo($completedMedia['file'], PATHINFO_EXTENSION)
                );
                $file = config('app.cdn.videos').$completedMedia['file'];
                $cover = config('app.cdn.covers').$basename.'.jpg';
                $medias[] = [
                    '_id' => new ObjectId($completedMedia['_id']),
                    'file' => $file,
                    'cover' => $cover,
                    'status' => $completedMedia['status'],
                ];
            }
            $creatorUser->medias = $medias;
            $creatorUser->count_fields_updated_at = array_merge(
                $creatorUser->count_fields_updated_at,
                ['medias' => DT::utcNow()]
            );
            $creatorUser->save();
        }

        if (! config('app.is_testing')) {
            UploadMedia::dispatch($media->userObj->_id, $media->_id, $media->file)->delay(10);
        }
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
            $model->people = User::select(['username', 'avatar', '_id'])->usernames($extractedPeople)->limit(20)->get()->toArray();
        }

        if ($model->wasChanged('file') && str_contains($model->file, config('app.cdn.videos'))) {
            $model->file = str_replace(config('app.cdn.videos'), '', $model->file);
        }

        if ($model->status === MediaStatus::DENIED->value) {
            $model->visibility = MediaVisibility::PRIVATE->value;
        }
    }

    /**
     * Create a new event instance.
     *
     * @param Media $model
     * @return void
     * @throws Exception
     */
    public function creating($model): void
    {
        $model->slug = MediaService::generateSlug(6);

        parent::creating($model);
    }

    /**
     * Create a new event instance.
     *
     * @param Media $media
     * @return void
     * @throws Exception
     */
    public function saved($media): void
    {
        if ($media->status === MediaStatus::USER_DELETED->value && $media->isDirty('status')) {
            $media->userObj->media_count = Media::query()->creator($media->creator['_id'])->availableForOwner()->count();

            $file = config('app.cdn.videos').$media->file;
            $cover = config('app.cdn.covers').$media->filename.'.jpg';
            $media->userObj->removeFromSet(
                'medias',
                ['_id' => $media->_id, 'file' => $file, 'cover' => $cover, 'status' => $media->status]
            );
            $media->userObj->count_fields_updated_at = array_merge(
                $media->userObj->count_fields_updated_at,
                ['medias' => DT::utcNow()]
            );
            $media->userObj->save();

            DeleteMediaLikes::dispatch((string) $media->_id)->onQueue('low');
            DeleteMediaComments::dispatch((string) $media->_id)->onQueue('low');
            DeleteMediaUserNotifications::dispatch((string) $media->_id)->onQueue('low');
            $media->unsearchable();
        }

        if ($media->wasChanged(['status', 'visibility'])) {
            foreach ($media->hashtags as $tag) {
                RecalculateHashtag::dispatch($tag);
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
    public function deleted($media): void
    {
        $creatorUser = $media->userObj;
        $creatorUser->media_count = Media::query()->creator($media->creator['_id'])->availableForOwner()->count();

        $file = config('app.cdn.videos').$media->file;
        $cover = config('app.cdn.covers').$media->filename.'.jpg';
        $creatorUser->removeFromSet('medias', ['_id' => $media->_id, 'file' => $file, 'cover' => $cover, 'status' => $media->status]);
        $creatorUser->count_fields_updated_at = array_merge(
            $creatorUser->count_fields_updated_at,
            ['medias' => DT::utcNow()]
        );
        $creatorUser->save();

        DeleteMediaLikes::dispatch((string) $media->_id)->onQueue('low');

        $creatorUser->notify(new UserDeleteMedia());
    }
}
