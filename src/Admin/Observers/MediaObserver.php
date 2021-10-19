<?php

namespace Aparlay\Core\Admin\Observers;

use Aparlay\Core\Admin\Models\Media;
use Aparlay\Core\Admin\Models\Media as ModelsMedia;
use Aparlay\Core\Admin\Models\User;
use Aparlay\Core\Admin\Models\User as ModelsUser;
use Aparlay\Core\Admin\Services\MediaService;
use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Jobs\DeleteMediaLike;
use Aparlay\Core\Jobs\UploadMedia;
use Exception;
use MongoDB\BSON\ObjectId;

class MediaObserver
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
    }
}
