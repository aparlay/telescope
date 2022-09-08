<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Database\Factories\EmailFactory;
use Aparlay\Core\Models\Enums\EmailStatus;
use Aparlay\Core\Models\Enums\EmailType;
use Aparlay\Core\Models\Queries\EmailQueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Jenssegers\Mongodb\Relations\BelongsTo;

/**
 * Class Email.
 *
 * @property int  $status
 * @property null $user_id
 * @property User $userObj
 *
 */
class Email extends BaseModel
{
    use HasFactory;
    use Notifiable;

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
     * Get the user associated with the tip.
     */
    public function userObj(): \Illuminate\Database\Eloquent\Relations\BelongsTo|BelongsTo
    {
        return $this->belongsTo(User::class, 'user._id');
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return EmailFactory::new();
    }

    /**
     * @return EmailQueryBuilder|Builder
     */
    public static function query(): EmailQueryBuilder|Builder
    {
        return parent::query();
    }

    /**
     * @param $query
     * @return EmailQueryBuilder
     */
    public function newEloquentBuilder($query): EmailQueryBuilder
    {
        return new EmailQueryBuilder($query);
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
