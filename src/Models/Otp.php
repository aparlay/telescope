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
 * @property string      $device_id
 * @property UTCDateTime $expired_at
 * @property string      $identity
 * @property int         $incorrect
 * @property string      $otp
 * @property int         $type
 * @property UTCDateTime $updated_at
 * @property int         $validation
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
    protected $fillable   = [
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
    protected $hidden     = [

    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts      = [
    ];
    protected $dates      = [
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

    public static function query(): OtpQueryBuilder|Builder
    {
        return parent::query();
    }

    public function newEloquentBuilder($query): OtpQueryBuilder
    {
        return new OtpQueryBuilder($query);
    }
}
