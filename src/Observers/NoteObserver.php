<?php

namespace Aparlay\Core\Observers;

use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Models\Follow;
use Aparlay\Core\Models\Note;
use Illuminate\Support\Facades\Redis;
use MongoDB\BSON\ObjectId;

class NoteObserver extends BaseModelObserver
{
    /**
     * Handle the Follow "creating" event.
     *
     * @param  Follow  $model
     * @return void
     */
    public function creating($model): void
    {
        
        parent::creating($model);
    }

    /**
     * Handle the Follow "created" event.
     *
     * @param  Follow  $model
     * @return void
     */
    public function created($model): void
    {
       
    }

   
}
