<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Events\ModelSaving;
use Illuminate\Support\Facades\Auth;
use MongoDB\BSON\ObjectId;

class Model extends \Jenssegers\Mongodb\Eloquent\Model
{
    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'saving' => ModelSaving::class,
    ];

    /**
     *
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            $loggedInUser = Auth::user();
            $model->created_by = !is_null($loggedInUser) ? new ObjectId($loggedInUser->_id) : $model->created_by;
            $model->updated_by = !is_null($loggedInUser) ? new ObjectId($loggedInUser->_id) : $model->updated_by;
        });

        static::updating(function ($model) {
            $model->updated_by = !is_null($loggedInUser = Auth::user()) ? new ObjectId($loggedInUser->_id) : $model->updated_by;
        });
    }
}
