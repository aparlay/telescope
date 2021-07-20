<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Database\Factories\MediaFactory;
use Aparlay\Core\Models\Scopes\MediaScope;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Jenssegers\Mongodb\Eloquent\Model;

class Media extends Model
{
    use HasFactory;
    use Notifiable;
    use MediaScope;

    public const VISIBILITY_PUBLIC = 1;
    public const VISIBILITY_PRIVATE = 0;

    public const STATUS_QUEUED = 0;
    public const STATUS_UPLOADED = 1;
    public const STATUS_IN_PROGRESS = 2;
    public const STATUS_COMPLETED = 3;
    public const STATUS_FAILED = 4;
    public const STATUS_CONFIRMED = 5;
    public const STATUS_DENIED = 6;
    public const STATUS_IN_REVIEW = 7;
    public const STATUS_ADMIN_DELETED = 9;
    public const STATUS_USER_DELETED = 10;

    /**
     * The collection associated with the model.
     * @var string
     */
    protected $collection = 'medias';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        '_id',
        'description',
        'notes',
        'location',
        'hash',
        'file',
        'files_history',
        'mime_type',
        'size',
        'length',
        'length_watched',
        'type',
        'like_count',
        'likes',
        'visit_count',
        'visits',
        'comment_count',
        'comments',
        'count_fields_updated_at',
        'visibility',
        'status',
        'is_music_licensed',
        'hashtags',
        'people',
        'processing_log',
        'blocked_user_ids',
        'creator',
        'scores',
        'sort_score',
        'slug',
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
        'email_verified_at' => 'datetime',
        'phone_number_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return Factory
     */
    protected static function newFactory(): Factory
    {
        return MediaFactory::new();
    }

    public static function getVisibilities()
    {
        return [
            self::VISIBILITY_PRIVATE => 'Private',
            self::VISIBILITY_PUBLIC => 'Public',
        ];
    }

    public static function getStatuses()
    {
        return [
            self::STATUS_QUEUED => 'Queued',
            self::STATUS_UPLOADED => 'Uploaded',
            self::STATUS_IN_PROGRESS => 'In-Progress',
            self::STATUS_COMPLETED => 'Waiting For Review',
            self::STATUS_FAILED => 'Failed',
            self::STATUS_CONFIRMED => 'Confirmed',
            self::STATUS_DENIED => 'Denied',
            self::STATUS_ADMIN_DELETED => 'Deleted By Admin',
            self::STATUS_USER_DELETED => 'Deleted',
            self::STATUS_IN_REVIEW => 'Under review'
        ];
    }
}
