<?php


namespace Aparlay\Core\Models;

use Illuminate\Support\Facades\Auth;
use MongoDB\BSON\ObjectId;

class Model extends \Jenssegers\Mongodb\Eloquent\Model
{
    /**
     *
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            $loggedInUser = Auth::user();
            $model->created_by = is_object($loggedInUser) ? new ObjectId($loggedInUser->_id) : null;
            $model->updated_by = is_object($loggedInUser) ? new ObjectId($loggedInUser->_id) : null;
        });

        static::updating(function ($model) {
            $model->updated_by = is_object($loggedInUser = Auth::user()) ? new ObjectId($loggedInUser->_id) : null;
        });
    }
}
