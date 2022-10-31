<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Casts\SimpleUserCast;
use Aparlay\Core\Database\Factories\MediaCommentLikeFactory;
use Aparlay\Core\Models\Queries\MediaCommentLikeQueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Relations\BelongsTo;
use MongoDB\BSON\ObjectId;

/**
 * Class MediaCommentLike.
 *
 * @property ObjectId   $_id
 * @property ObjectId   $media_id
 * @property ObjectId   $user_id
 * @property array      $creator
 * @property string     $created_at
 * @property User       $creatorObj
 * @property mixed|null $creator_id
 * @property Media      $mediaCommentObj
 * @property User       $userObj
 *
 * @method static |self|Builder media(ObjectId|string $mediaId)            get liked media
 * @method static |self|Builder user(ObjectId|string $userId)              get user who liked media
 * @method static |self|Builder creator(ObjectId|string $creatorId)        get creator user who liked media
 */
class MediaCommentLike extends BaseModel
{
    use HasFactory;

    /**
     * The collection associated with the model.
     *
     * @var string
     */
    protected $collection = 'media_comment_likes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        '_id',
        'media_comment_id',
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
        return MediaCommentLikeFactory::new();
    }

    /**
     * @return MediaCommentLikeQueryBuilder|Builder
     */
    public static function query(): MediaCommentLikeQueryBuilder|Builder
    {
        return parent::query();
    }

    /**
     * @param $query
     * @return MediaCommentLikeQueryBuilder
     */
    public function newEloquentBuilder($query)
    {
        return new MediaCommentLikeQueryBuilder($query);
    }

    /**
     * Get the user associated with the alert.
     */
    public function creatorObj(): \Illuminate\Database\Eloquent\Relations\BelongsTo | BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the media comment associated with the media comment like.
     */
    public function mediaCommentObj(): \Illuminate\Database\Eloquent\Relations\BelongsTo | BelongsTo
    {
        return $this->belongsTo(MediaComment::class, 'media_comment_id');
    }
}
