<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Database\Factories\OtpFactory;
use Aparlay\Core\Models\Queries\OtpQueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

/**
 * Class Otp.
 *
 * @property ObjectId    $_id
 * @property UTCDateTime $created_at
 * @property UTCDateTime $updated_at
 * @property UTCDateTime $expired_at
 * @property int         $incorrect
 * @property int         $type
 * @property int         $validation
 * @property string      $device_id
 * @property string      $identity
 * @property string      $otp
 */
class Otp extends BaseModel
{
    use HasFactory;
    use Notifiable;

    /**
     * The collection associated with the model.
     *
     * @var string
     */
    protected $collection = 'otps';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        '_id',
        'identity',
        'otp',
        'device_id',
        'type',
        'validated',
        'incorrect',
        'expired_at',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [

    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'expired_at',
    ];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return OtpFactory::new();
    }

    /**
     * @return OtpQueryBuilder|Builder
     */
    public static function query(): OtpQueryBuilder|Builder
    {
        return parent::query();
    }

    /**
     * @param $query
     *
     * @return OtpQueryBuilder
     */
    public function newEloquentBuilder($query): OtpQueryBuilder
    {
        return new OtpQueryBuilder($query);
    }
}
