<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Database\Factories\ReportFactory;
use Aparlay\Core\Models\Enums\ReportStatus;
use Aparlay\Core\Models\Enums\ReportType;
use Aparlay\Core\Models\Queries\ReportQueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use MongoDB\BSON\ObjectId;

/**
 * Class Report.
 *
 * @property ObjectId     $_id
 * @property ObjectId     $comment_id
 * @property string       $created_at
 * @property ObjectId     $created_by
 * @property User         $creatorObj
 * @property string       $deleted_at
 * @property array        $links
 * @property ObjectId     $media_id
 * @property MediaComment $mediaCommentObj
 * @property Media        $mediaObj
 * @property string       $reason
 * @property int          $status
 * @property int          $type
 * @property string       $updated_at
 * @property ObjectId     $updated_by
 * @property ObjectId     $user_id
 * @property User         $userObj
 * @property-read string slack_subject_admin_url
 */
class Report extends BaseModel
{
    use HasFactory;
    use Notifiable;

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
    protected $fillable   = [
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
    protected $hidden     = [
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts      = [
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

    public static function query(): ReportQueryBuilder|Builder
    {
        return parent::query();
    }

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param \Illuminate\Database\Query\Builder $query
     */
    public function newEloquentBuilder($query): ReportQueryBuilder
    {
        return new ReportQueryBuilder($query);
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
     * Get the phone associated with the user.
     */
    public function mediaCommentObj()
    {
        return $this->belongsTo(MediaComment::class, 'comment_id');
    }

    /**
     * Get the user associated with the report.
     */
    public function creatorObj(): \Illuminate\Database\Eloquent\Relations\BelongsTo|BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getSlackSubjectAdminUrlAttribute(): string
    {
        return match ($this->type) {
            ReportType::COMMENT->value => $this->mediaCommentObj->slack_admin_url ?? '',
            ReportType::USER->value => $this->userObj->slack_admin_url            ?? '',
            ReportType::MEDIA->value => $this->mediaObj->slack_admin_url          ?? '',
            default => '',
        };
    }

    public static function getStatuses(): array
    {
        return [
            ReportStatus::REPORTED->value => ReportStatus::REPORTED->label(),
            ReportStatus::REVISED->value => ReportStatus::REVISED->label(),
        ];
    }

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
     */
    public function routeNotificationForSlack($notification): string
    {
        return config('app.slack_webhook_url');
    }
}
