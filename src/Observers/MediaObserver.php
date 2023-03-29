<?php

namespace Aparlay\Core\Observers;

use Aparlay\Core\Api\V1\Notifications\UserDeleteMedia;
use Aparlay\Core\Api\V1\Services\MediaService;
use Aparlay\Core\Jobs\DeleteMediaComments;
use Aparlay\Core\Jobs\DeleteMediaLikes;
use Aparlay\Core\Jobs\DeleteMediaMetadata;
use Aparlay\Core\Jobs\DeleteMediaUserNotifications;
use Aparlay\Core\Jobs\PurgeMediaJob;
use Aparlay\Core\Jobs\RecalculateHashtag;
use Aparlay\Core\Jobs\UploadMedia;
use Aparlay\Core\Models\Enums\MediaStatus;
use Aparlay\Core\Models\Enums\MediaVisibility;
use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\User;
use Exception;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Redis;

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
            $creatorUser->updateMedias();
        }

        if (! config('app.is_testing')) {
            Bus::chain([
                new DeleteMediaMetadata($media->file),
                (new UploadMedia($media->userObj->_id, $media->_id, $media->file))->delay(10),
            ])
            ->onQueue(config('app.server_specific_queue'))
            ->dispatch();
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
        if (in_array($media->status, [MediaStatus::USER_DELETED->value, MediaStatus::ADMIN_DELETED->value])
            && $media->isDirty('status')) {
            $media->userObj->updateMedias();

            DeleteMediaLikes::dispatch((string) $media->_id)->onQueue('low');
            DeleteMediaComments::dispatch((string) $media->_id)->onQueue('low');
            DeleteMediaUserNotifications::dispatch((string) $media->_id)->onQueue('low');
            PurgeMediaJob::dispatchIf(! config('app.is_testing'), (string) $media->_id)->onQueue('low');
            $media->unsearchable();
        }

        if ($media->wasChanged(['status', 'visibility'])) {
            Media::CachePublicExplicitMediaIds();
            Media::CachePublicToplessMediaIds();
            Media::CachePublicMediaIds();
        }

        if ($media->wasChanged(['hashtags'])) {
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
        $creator = $media->userObj;
        $creator->updateMedias();

        DeleteMediaLikes::dispatch((string) $media->_id)->onQueue('low');

        $creator->notify(new UserDeleteMedia());
    }
}
