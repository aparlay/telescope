<?php

namespace Aparlay\Core\Observers;

use Aparlay\Core\Models\UserDocument;

class UserDocumentObserver extends BaseModelObserver
{
    /**
     * Handle the Block "creating" event.
     *
     * @param  UserDocument  $model
     * @return void
     */
    public function creating($model): void
    {
        parent::creating($model);
    }

    /**
     * Handle the Block "created" event.
     *
     * @param  UserDocument  $model
     * @return void
     */
    public function created($model): void
    {
    }

    /**
     * Handle the Block "deleted" event.
     *
     * @param  UserDocument  $model
     * @return void
     */
    public function deleted($model): void
    {
    }
}
