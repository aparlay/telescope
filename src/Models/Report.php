<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Database\Factories\ReportFactory;
use Aparlay\Core\Models\Enums\ReportStatus;
use Aparlay\Core\Models\Enums\ReportType;
use Aparlay\Core\Models\Scopes\ReportScope;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Jenssegers\Mongodb\Relations\BelongsTo;
use MongoDB\BSON\ObjectId;

/**
 * Class Report.
 *
 * @property ObjectId $_id
 * @property ObjectId $user_id
 * @property ObjectId $media_id
 * @property ObjectId $comment_id
 * @property int      $type
 * @property int      $status
 * @property string   $reason
 * @property ObjectId $created_by
 * @property ObjectId $updated_by
 * @property string   $created_at
 * @property string   $updated_at
 * @property string   $deleted_at
 * @property array    $links
 *
 * @property User $userObj
 * @property Media $mediaObj
 */
class Report extends BaseModel
{
    use HasFactory;
    use Notifiable;
    use ReportScope;

    /**
     * The collection associated with the model.
     *
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
        'reason' => 'string',
        'type' => 'integer',
        'status' => 'integer',
    ];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return ReportFactory::new();
    }

    /**
     * Get the phone associated with the user.
     */
    public function userObj()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the phone associated with the user.
     */
    public function mediaObj()
    {
        return $this->belongsTo(Media::class, 'media_id');
    }

    /**
     * Get the user associated with the report.
     */
    public function creatorObj(): \Illuminate\Database\Eloquent\Relations\BelongsTo | BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return string
     */
    public function getSlackSubjectAdminUrlAttribute(): string
    {
        return match ($this->type) {
            ReportType::USER->value => $this->userObj->slack_admin_url,
            ReportType::MEDIA->value => $this->mediaObj->slack_admin_url,
            default => '',
        };
    }

    /**
     * @return array
     */
    public static function getStatuses(): array
    {
        return [
            ReportStatus::REPORTED->value => ReportStatus::REPORTED->label(),
            ReportStatus::REVISED->value => ReportStatus::REVISED->label(),
        ];
    }

    /**
     * @return array
     */
    public static function getTypes(): array
    {
        return [
            ReportType::USER->value => ReportType::USER->label(),
            ReportType::MEDIA->value => ReportType::MEDIA->label(),
            ReportType::COMMENT->value => ReportType::COMMENT->label(),
        ];
    }

    /**
     * Route notifications for the Slack channel.
     *
     * @param Notification $notification
     *
     * @return string
     */
    public function routeNotificationForSlack($notification): string
    {
        return config('app.slack_webhook_url');
    }
}
