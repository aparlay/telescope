<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Database\Factories\UserNotificationFactory;
use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Models\Enums\UserNotificationCategory;
use Aparlay\Core\Models\Enums\UserNotificationStatus;
use Aparlay\Core\Models\Queries\UserNotificationQueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Relation;
use Jenssegers\Mongodb\Relations\BelongsTo;
use MongoDB\BSON\ObjectId;

/**
 * Class Alert.
 *
 * @property ObjectId $_id
 * @property int      $category
 * @property string   $created_at
 * @property ObjectId $created_by
 * @property array    $entity
 * @property mixed    $entityObj
 * @property string   $reason
 * @property int      $status
 * @property string   $updated_at
 * @property ObjectId $updated_by
 * @property array    $user
 * @property-read string $category_label
 * @property-read string $status_label
 * @property-read User $userObj
 */
class UserNotification extends BaseModel
{
    use HasFactory;

    /**
     * The collection associated with the model.
     *
     * @var string
     */
    protected $collection = 'user_notifications';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable   = [
        '_id',
        'user_id',
        'entity._id',
        'entity._type',
        'category',
        'status',
        'message',
        'payload',
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
    protected $casts      = [
        'message' => 'string',
        'category' => 'integer',
        'status' => 'integer',
    ];

    public static function boot()
    {
        parent::boot();

        // to keep entities in database without namespace
        Relation::morphMap([
            'Media' => Media::class,
            'User' => User::class,
            'Tip' => 'Aparlay\Payment\Models\Tip',
        ]);
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return UserNotificationFactory::new();
    }

    public static function query(): UserNotificationQueryBuilder|Builder
    {
        return parent::query();
    }

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param \Illuminate\Database\Query\Builder $query
     */
    public function newEloquentBuilder($query): UserNotificationQueryBuilder
    {
        return new UserNotificationQueryBuilder($query);
    }

    /**
     * Get the user associated with the alert.
     */
    public function userObj(): \Illuminate\Database\Eloquent\Relations\BelongsTo|BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the parent userNotifiable model (user or post).
     */
    public function entityObj(): MorphTo|\Jenssegers\Mongodb\Relations\MorphTo
    {
        return $this->morphTo('entity.');
    }

    public function getStatusLabelAttribute(): string
    {
        return UserNotificationStatus::from($this->status)->label();
    }

    public function getCategoryLabelAttribute(): string
    {
        return UserNotificationCategory::from($this->category)->label();
    }

    public function regenerateMessage(): void
    {
        if ($this->category === UserNotificationCategory::LIKES->value) {
            $this->regenerateLikeNotification();
        }

        if ($this->category === UserNotificationCategory::COMMENTS->value) {
            $this->regenerateCommentNotification();
        }

        if ($this->category === UserNotificationCategory::FOLLOWS->value) {
            $this->regenerateFollowNotification();
        }
    }

    private function regenerateLikeNotification(): bool
    {
        /** @var Media $media */
        $media            = $this->entityObj;
        $this->message    = $media->likesNotificationMessage();
        $this->created_at = DT::utcNow();

        return $this->save();
    }

    private function regenerateCommentNotification(): bool
    {
        /** @var Media $media */
        $media            = $this->entityObj;
        $this->message    = $media->commentsNotificationMessage();
        $this->created_at = DT::utcNow();

        return $this->save();
    }

    private function regenerateFollowNotification(): bool
    {
        $this->created_at = DT::utcNow();

        return $this->save();
    }
}
