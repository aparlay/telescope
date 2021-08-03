<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Database\Factories\MediaLikeFactory;
use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Models\Scopes\MediaLikeScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Jenssegers\Mongodb\Relations\BelongsTo;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

/**
 * Class MediaLike
 * @package Aparlay\Core\Models
 *
 * @property ObjectId $_id
 * @property string $hashtag
 * @property ObjectId $media_id
 * @property ObjectId $user_id
 * @property array $creator
 * @property string $created_at
 *
 * @property-read User $creatorObj
 * @property-read null|mixed $creator_id
 * @property-read Media $mediaObj
 * @property-read User $userObj
 *
 * @method static |self|Builder media(ObjectId|string $mediaId) get liked media
 * @method static |self|Builder user(ObjectId|string $userId) get user who liked media
 * @method static |self|Builder creator(ObjectId|string $creatorId) get creator user who liked media
 * @method static |self|Builder date(UTCDateTime $start, UTCDateTime $end) get date of like
 */
class MediaLike extends Model
{
    use HasFactory;
    use Notifiable;
    use MediaLikeScope;

    /**
     * The collection associated with the model.
     * @var string
     */
    protected $collection = 'media_likes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        '_id',
        'media_id',
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
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::created(function ($like) {
            $like->mediaObj->like_count++;
            $like->mediaObj->addToSet('likes', [
                '_id' => new ObjectId($like->creator['_id']),
                'username' => $like->creator['username'],
                'avatar' => $like->creator['avatar'],
            ]);
            $like->mediaObj->count_fields_updated_at = array_merge(
                $like->mediaObj->count_fields_updated_at,
                ['likes' => DT::utcNow()]
            );
            $like->mediaObj->save();

            $like->mediaObj->userObj->like_count++;
            $like->mediaObj->userObj->addToSet('likes', [
                '_id' => new ObjectId($like->creator['_id']),
                'username' => $like->creator['username'],
                'avatar' => $like->creator['avatar'],
            ]);
            $like->mediaObj->userObj->count_fields_updated_at = array_merge(
                $like->mediaObj->userObj->count_fields_updated_at,
                ['likes' => DT::utcNow()]
            );
            $like->mediaObj->userObj->save();
        });

        static::deleted(function ($like) {
            $like->mediaObj->like_count--;
            $like->mediaObj->removeFromSet('likes', [
                '_id' => new ObjectId($like->creator['_id']),
                'username' => $like->creator['username'],
                'avatar' => $like->creator['avatar'],
            ]);
            $like->mediaObj->count_fields_updated_at = array_merge(
                $like->mediaObj->count_fields_updated_at,
                ['likes' => DT::utcNow()]
            );
            $like->mediaObj->save();

            $like->mediaObj->userObj->like_count--;
            $like->mediaObj->userObj->removeFromSet('likes', [
                '_id' => new ObjectId($like->creator['_id']),
                'username' => $like->creator['username'],
                'avatar' => $like->creator['avatar'],
            ]);
            $like->mediaObj->userObj->count_fields_updated_at = array_merge(
                $like->mediaObj->userObj->count_fields_updated_at,
                ['likes' => DT::utcNow()]
            );
            $like->mediaObj->userObj->save();
        });
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return Factory
     */
    protected static function newFactory(): Factory
    {
        return MediaLikeFactory::new();
    }

    /**
     * Get the user associated with the alert.
     */
    public function userObj(): \Illuminate\Database\Eloquent\Relations\BelongsTo|BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the media associated with the alert.
     */
    public function mediaObj(): \Illuminate\Database\Eloquent\Relations\BelongsTo|BelongsTo
    {
        return $this->belongsTo(Media::class, 'media_id');
    }
}
