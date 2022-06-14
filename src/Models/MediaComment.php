<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Casts\SimpleUserCast;
use Aparlay\Core\Database\Factories\MediaCommentFactory;
use Aparlay\Core\Models\Queries\MediaCommentQueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Relations\BelongsTo;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

/**
 * Class MediaLike.
 *
 * @property ObjectId   $_id
 * @property string     $hashtag
 * @property ObjectId   $media_id
 * @property ObjectId   $user_id
 * @property array      $creator
 * @property string     $created_at
 * @property User       $creatorObj
 * @property mixed|null $creator_id
 * @property Media      $mediaObj
 * @property MediaComment $parentObj
 * @property MediaComment $replyToObj
 * @property User       $userObj
 *
 * @method static |self|Builder media(ObjectId|string $mediaId)            get liked media
 * @method static |self|Builder user(ObjectId|string $userId)              get user who liked media
 * @method static |self|Builder creator(ObjectId|string $creatorId)        get creator user who liked media
 * @method static |self|Builder date(UTCDateTime $start, UTCDateTime $end) get date of like
 */
class MediaComment extends BaseModel
{
    use HasFactory;

    /**
     * The collection associated with the model.
     *
     * @var string
     */
    protected $collection = 'media_comment';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        '_id',
        'media_id',
        'reply_to_user',
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
     * @param $query
     * @return MediaCommentQueryBuilder
     */
    public function newEloquentBuilder($query): MediaCommentQueryBuilder
    {
        return new MediaCommentQueryBuilder($query);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|\Jenssegers\Mongodb\Relations\HasMany
     */
    public function lastRepliesObjs()
    {
        return $this->hasMany(self::class, 'parent._id')
            ->limit(10)
            ->latest();
    }

    public function parentObj()
    {
        return $this->belongsTo(self::class, 'parent._id');
    }



    /**
     * Get the user associated with the alert.
     */
    public function userObj(): \Illuminate\Database\Eloquent\Relations\BelongsTo | BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user associated with the alert.
     */
    public function creatorObj(): \Illuminate\Database\Eloquent\Relations\BelongsTo | BelongsTo
    {
        return $this->belongsTo(User::class, 'creator._id');
    }

    /**
     * Get the media associated with the alert.
     */
    public function mediaObj(): \Illuminate\Database\Eloquent\Relations\BelongsTo | BelongsTo
    {
        return $this->belongsTo(Media::class, 'media_id');
    }
}
