<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Database\Factories\EmailFactory;
use Aparlay\Core\Models\Enums\EmailStatus;
use Aparlay\Core\Models\Enums\EmailType;
use Aparlay\Core\Models\Scopes\EmailScope;
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
    use EmailScope;

    public const TEMPLATE_EMAIL_VERIFICATION = 'email_verification';
    public const TEMPLATE_EMAIL_CONTACTUS = 'email_contactus';

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
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return EmailFactory::new();
    }

    /**
     * @return array
     */
    public static function getStatuses(): array
    {
        return [
            EmailStatus::QUEUED->value => EmailStatus::QUEUED->label(),
            EmailStatus::SENT->value => EmailStatus::SENT->label(),
            EmailStatus::OPENED->value => EmailStatus::OPENED->label(),
            EmailStatus::FAILED->value => EmailStatus::FAILED->label(),
        ];
    }

    /**
     * @return array
     */
    public static function getTypes(): array
    {
        return [
            EmailType::OTP->value => EmailType::OTP->label(),
            EmailType::CONTACT->value => EmailType::CONTACT->label(),
        ];
    }

    /**
     * @return array
     */
    public static function getTemplates(): array
    {
        return [
            EmailType::OTP->value => self::TEMPLATE_EMAIL_VERIFICATION,
            EmailType::CONTACT->value => self::TEMPLATE_EMAIL_CONTACTUS,
        ];
    }
}
