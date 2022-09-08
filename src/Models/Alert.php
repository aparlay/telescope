<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Database\Factories\AlertFactory;
use Aparlay\Core\Models\Enums\AlertStatus;
use Aparlay\Core\Models\Enums\AlertType;
use Aparlay\Core\Models\Queries\AlertQueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;
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
 */
class Alert extends BaseModel
{
    use HasFactory;
    use Notifiable;

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
        'entity._id',
        'entity._type',
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
     * @return AlertQueryBuilder|Builder
     */
    public static function query(): AlertQueryBuilder|Builder
    {
        return parent::query();
    }

    /**
     * @param $query
     * @return AlertQueryBuilder
     */
    public function newEloquentBuilder($query): AlertQueryBuilder
    {
        return new AlertQueryBuilder($query);
    }

    /**
     * Get the user associated with the alert.
     */
    public function userObj(): \Illuminate\Database\Eloquent\Relations\BelongsTo | BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function entityObj(): MorphTo|\Jenssegers\Mongodb\Relations\MorphTo
    {
        return $this->morphTo('entity.');
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
