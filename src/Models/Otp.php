<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Database\Factories\OtpFactory;
use Aparlay\Core\Models\Scopes\OtpScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use MongoDB\BSON\ObjectId;

/**
 * @method static |self|Builder identity(string $identity)    get otp
 * @method static |self|Builder otp(string $otp)    get otp
 * @method static |self|Builder validated(bool $checkValidated)    get otp
 * @method static |self|Builder recentFirst()    sort otp
 * @method static |self|Builder remainingAttempt(int $limit)    get otp
 */
class Otp extends BaseModel
{
    use HasFactory;
    use Notifiable;
    use OtpScope;

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
}
