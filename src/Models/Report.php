<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Database\Factories\ReportFactory;
use Aparlay\Core\Models\Scopes\ReportScope;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notifiable;
use Jenssegers\Mongodb\Eloquent\Model;
use MongoDB\BSON\ObjectId;

/**
 * Class Report
 * @package Aparlay\Core\Models
 *
 * @property ObjectId $_id
 * @property ObjectId $user_id
 * @property ObjectId $media_id
 * @property ObjectId $comment_id
 * @property int $type
 * @property int $status
 * @property string $reason
 * @property ObjectId $created_by
 * @property ObjectId $updated_by
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 *
 * @property-read array $links
 *
 * @OA\Schema()
 */
class Report extends Model
{
    use HasFactory;
    use Notifiable;
    use ReportScope;

    public const TYPE_USER = 0;
    public const TYPE_MEDIA = 1;
    public const TYPE_COMMENT = 2;

    public const STATUS_REPORTED = 0;
    public const STATUS_REVISED = 1;

    /**
     * The collection associated with the model.
     * @var string
     */
    protected $collection = 'reports';

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
        'comment_id',
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
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        '_id' => 'string',
        'user_id' => 'string',
        'media_id' => 'string',
        'comment_id' => 'string',
        'created_by' => 'string',
        'updated_by' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * @return array
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_REPORTED => __('Reported'),
            self::STATUS_REVISED => __('Revised'),
        ];
    }

    /**
     * @return array
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_COMMENT => __('Comment'),
            self::TYPE_MEDIA => __('Media'),
            self::TYPE_USER => __('User'),
        ];
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return Factory
     */
    protected static function newFactory(): Factory
    {
        return ReportFactory::new();
    }
}
