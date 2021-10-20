<?php

namespace Aparlay\Core\Admin\Observers;

use Aparlay\Core\Admin\Models\Media;
use Exception;

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
        if ($model->wasChanged('file') && str_contains($model->file, config('app.cdn.videos'))) {
            $model->file = str_replace(config('app.cdn.videos'), '', $model->file);
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
