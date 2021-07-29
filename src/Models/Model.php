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

    /**
     * @param string $attribute
     * @param mixed $item
     * @param  int|null  $length
     */
    public function addToSet(string $attribute, mixed $item, int $length = null): void
    {
        if (!is_array($this->$attribute)) {
            $this->$attribute = [];
        }
        $values = $this->$attribute;
        if (!in_array($item, $values, false)) {
            array_unshift($values, $item);
        }

        if ($length !== null) {
            $values = array_slice($values, 0, $length);
        }

        $this->$attribute = $values;
    }

    /**
     * @param string $attribute
     * @param mixed $item
     */
    public function removeFromSet(string $attribute, mixed $item): void
    {
        if (!is_array($this->$attribute)) {
            $this->$attribute = [];
        }
        $values = $this->$attribute;
        if (($key = array_search($item, $values, false)) !== false) {
            unset($values[$key]);
            if (is_int($key)) {
                $values = array_values($values);
            }
        }

        $this->$attribute = $values;
    }
}
