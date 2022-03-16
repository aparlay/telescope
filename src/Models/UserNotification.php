<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Database\Factories\AlertFactory;
use Aparlay\Core\Models\Enums\UserNotificationStatus;
use Aparlay\Core\Models\Scopes\UserNotificationScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Relations\BelongsTo;
use MongoDB\BSON\ObjectId;

/**
 * Class Alert.
 *
 * @property ObjectId $_id
 * @property ObjectId $user_id
 * @property ObjectId $entity_id
 * @property string $entity_type
 * @property string $reason
 * @property int $status
 * @property int $category
 * @property ObjectId $created_by
 * @property ObjectId $updated_by
 * @property string $created_at
 * @property string $updated_at
 *
 * @property-read string $category_label
 * @property-read string $status_label
 *
 * @method static |self|Builder visited()                       get visited alerts
 * @method static |self|Builder notVisited()                    get not visited alerts
 * @method static |self|Builder user(ObjectId|string $userId)   get user and media alerts
 */
class UserNotification extends BaseModel
{
    use HasFactory;
    use UserNotificationScope;

    /**
     * The collection associated with the model.
     *
     * @var string
     */
    protected $collection = 'notifications';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        '_id',
        'user_id',
        'entity_id',
        'entity_type',
        'category',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'message' => 'string',
        'category' => 'integer',
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
     * Get the parent imageable model (user or post).
     */
    public function entity()
    {
        return $this->morphTo();
    }

    public function getStatusLabelAttribute(): string
    {
        return UserNotificationStatus::from($this->status)->label();
    }

    public function getCategoryLabelAttribute(): string
    {
        return UserNotificationStatus::from($this->status)->label();
    }
}
