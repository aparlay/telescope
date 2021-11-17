<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Database\Factories\EmailFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use MongoDB\BSON\ObjectId;

/**
 * Class Email.
 *
 * @property int  $status
 * @property null $user_id
 * @property User $userObj
 *
 * @method static |self|Builder visited()                       get visited alerts
 * @method static |self|Builder notVisited()                    get not visited alerts
 * @method static |self|Builder media(ObjectId|string $mediaId) get media alerts
 * @method static |self|Builder user(ObjectId|string $userId)   get user alerts
 */
class Email extends BaseModel
{
    use HasFactory;
    use Notifiable;

    public const STATUS_QUEUED = 0;
    public const STATUS_SENT = 1;
    public const STATUS_OPENED = 2;
    public const STATUS_FAILED = 3;

    public const TYPE_OTP = 0;

    public const TEMPLATE_EMAIL_VERIFICATION = 'email_verification';

    /**
     * The collection associated with the model.
     *
     * @var string
     */
    protected $collection = 'emails';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        '_id',
        'user',
        'to',
        'status',
        'type',
        'tracking',
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
        'type' => 'integer',
        'status' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * @return array
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_QUEUED => __('Queued'),
            self::STATUS_SENT => __('Sent'),
            self::STATUS_OPENED => __('Opened'),
            self::STATUS_FAILED => __('Failed'),
        ];
    }

    /**
     * @return array
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_OTP => __('OTP'),
        ];
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return EmailFactory::new();
    }
}
