<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Database\Factories\AlertFactory;
use Aparlay\Core\Models\Enums\AlertStatus;
use Aparlay\Core\Models\Enums\AlertType;
use Aparlay\Core\Models\Scopes\AlertScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Jenssegers\Mongodb\Relations\BelongsTo;
use MongoDB\BSON\ObjectId;

/**
 * Class Alert.
 *
 * @property ObjectId $_id
 * @property ObjectId $user_id
 * @property ObjectId $media_id
 * @property string $reason
 * @property int $status
 * @property int $type
 * @property ObjectId $created_by
 * @property ObjectId $updated_by
 * @property string $created_at
 * @property string $updated_at
 * @property User $userObj
 * @property Media $mediaObj
 * @property User $creator
 * @property string $slack_subject_admin_url
 * @property string $aliasModel
 *
 * @method static |self|Builder visited()                       get visited alerts
 * @method static |self|Builder notVisited()                    get not visited alerts
 * @method static |self|Builder media(ObjectId|string $mediaId) get media alerts
 * @method static |self|Builder user(ObjectId|string $userId)   get user alerts
 */
class Alert extends BaseModel
{
    use HasFactory;
    use Notifiable;
    use AlertScope;

    /**
     * The collection associated with the model.
     *
     * @var string
     */
    protected $collection = 'alerts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        '_id',
        'reason',
        'user_id',
        'media_id',
        'type',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'reason' => 'string',
        'type' => 'integer',
        'status' => 'integer',
    ];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return AlertFactory::new();
    }

    /**
     * Get the user associated with the alert.
     */
    public function userObj(): \Illuminate\Database\Eloquent\Relations\BelongsTo | BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the media associated with the alert.
     */
    public function mediaObj(): \Illuminate\Database\Eloquent\Relations\BelongsTo | BelongsTo
    {
        return $this->belongsTo(Media::class, 'media_id');
    }

    /**
     * @return array
     */
    public static function getStatuses(): array
    {
        return [
            AlertStatus::NOT_VISITED->value => AlertStatus::NOT_VISITED->label(),
            AlertStatus::VISITED->value => AlertStatus::VISITED->label(),
        ];
    }

    /**
     * @return array
     */
    public static function getTypes(): array
    {
        return [
            AlertType::MEDIA_NOTICED->value => AlertType::MEDIA_NOTICED->label(),
            AlertType::MEDIA_REMOVED->value => AlertType::MEDIA_REMOVED->label(),
            AlertType::USER->value => AlertType::USER->label(),
        ];
    }
}
