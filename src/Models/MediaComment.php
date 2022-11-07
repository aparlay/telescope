<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Casts\SimpleUserCast;
use Aparlay\Core\Database\Factories\MediaCommentFactory;
use Aparlay\Core\Models\Queries\MediaCommentQueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Jenssegers\Mongodb\Relations\BelongsTo;
use MongoDB\BSON\ObjectId;

/**
 * Class MediaComment.
 *
 * @property ObjectId     $_id
 * @property ObjectId     $media_id
 * @property ObjectId     $tip_id
 * @property ObjectId     $user_id
 * @property array        $creator
 * @property string       $text
 * @property string       $created_at
 * @property User         $creatorObj
 * @property mixed|null   $creator_id
 * @property Media        $mediaObj
 * @property bool         $is_first
 * @property array|null   $first_reply
 * @property MediaComment $parentObj
 *
 * @property-read string  $slack_admin_url
 * @property-read string  $admin_url
 */
class MediaComment extends BaseModel
{
    use HasFactory;
    use Notifiable;

    /**
     * The collection associated with the model.
     *
     * @var string
     */
    protected $collection = 'media_comments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        '_id',
        'media_id',
        'tip_id',
        'reply_to_user',
        'first_reply',
        'parent',
        'text',
        'user_id',
        'creator',
        'created_at',
        'created_by',
        'updated_by',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [];

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
        'creator' => SimpleUserCast::class.':_id,username,avatar,is_liked,is_followed,is_verified',
    ];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return MediaCommentFactory::new();
    }

    /**
     * @return MediaCommentQueryBuilder|Builder
     */
    public static function query(): MediaCommentQueryBuilder|Builder
    {
        return parent::query();
    }

    /**
     * @param $query
     *
     * @return MediaCommentQueryBuilder
     */
    public function newEloquentBuilder($query): MediaCommentQueryBuilder
    {
        return new MediaCommentQueryBuilder($query);
    }

    public function parentObj()
    {
        return $this->belongsTo(self::class, 'parent._id');
    }

    /**
     * Get the user associated with the alert.
     */
    public function userObj(): \Illuminate\Database\Eloquent\Relations\BelongsTo|BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user associated with the alert.
     */
    public function creatorObj(): \Illuminate\Database\Eloquent\Relations\BelongsTo|BelongsTo
    {
        return $this->belongsTo(User::class, 'creator._id');
    }

    /**
     * Get the media associated with the alert.
     */
    public function mediaObj(): \Illuminate\Database\Eloquent\Relations\BelongsTo|BelongsTo
    {
        return $this->belongsTo(Media::class, 'media_id');
    }

    /**
     * @return string
     */
    public function getSlackAdminUrlAttribute(): string
    {
        return "<{$this->admin_url}|comment> By {$this->userObj->slack_admin_url}";
    }

    /**
     * @return string
     */
    public function getAdminUrlAttribute(): string
    {
        return route('core.admin.media.view', ['media' => $this->_id]);
    }
}
