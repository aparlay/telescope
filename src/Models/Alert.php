<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Database\Factories\AlertFactory;
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
class Alert extends Model
{
    use HasFactory;
    use Notifiable;
    use AlertScope;

    public const TYPE_USER = 0;

    public const TYPE_MEDIA_REMOVED = 20;

    public const TYPE_MEDIA_NOTICED = 21;

    public const STATUS_NOT_VISITED = 0;

    public const STATUS_VISITED = 1;

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
        '_id' => 'string',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
        'deleted_at' => 'timestamp',
    ];

    public static function getStatuses(): array
    {
        return [
            self::STATUS_NOT_VISITED => __('Not Visited'),
            self::STATUS_VISITED => __('Visited'),
        ];
    }

    public static function getTypes(): array
    {
        return [
            self::TYPE_MEDIA_NOTICED => __('Video Notice'),
            self::TYPE_MEDIA_REMOVED => __('Video Removed'),
            self::TYPE_USER => __('User'),
        ];
    }

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
}
