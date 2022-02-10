<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Casts\SimpleUserCast;
use Aparlay\Core\Database\Factories\MediaLikeFactory;
use Aparlay\Core\Models\Scopes\MediaLikeScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
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
 * @property User       $userObj
 *
 * @method static |self|Builder media(ObjectId|string $mediaId)            get liked media
 * @method static |self|Builder user(ObjectId|string $userId)              get user who liked media
 * @method static |self|Builder creator(ObjectId|string $creatorId)        get creator user who liked media
 * @method static |self|Builder date(UTCDateTime $start, UTCDateTime $end) get date of like
 */
class MediaLike extends BaseModel
{
    use HasFactory;
    use Notifiable;
    use MediaLikeScope;

    /**
     * The collection associated with the model.
     *
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
        'creator' => SimpleUserCast::class.':_id,username,avatar,is_liked,is_followed',
    ];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return MediaLikeFactory::new();
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
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the media associated with the alert.
     */
    public function mediaObj(): \Illuminate\Database\Eloquent\Relations\BelongsTo | BelongsTo
    {
        return $this->belongsTo(Media::class, 'media_id');
    }

    /**
     * @param  ObjectId|string  $userId
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public static function cacheByUserId(ObjectId | string $userId): void
    {
        $userId = $userId instanceof ObjectId ? (string) $userId : $userId;
        $cacheKey = self::getCollection().':creator:'.$userId;

        if (! Redis::exists($cacheKey)) {
            $likedMediaIds = self::project(['media_id' => true, '_id' => false])
                ->creator($userId)
                ->pluck('media_id')
                ->toArray();

            if (empty($likedMediaIds)) {
                $likedMediaIds = [''];
            }

            Cache::store('octane')->put($cacheKey, self::implode(',', $likedMediaIds), 300);

            Redis::sAdd($cacheKey, ...$likedMediaIds);
            Redis::expire($cacheKey, config('app.cache.veryLongDuration'));
        }

        if (Cache::store('octane')->get($cacheKey, false) === false) {
            $likedMediaIds = Redis::sMembers($cacheKey);

            Cache::store('octane')->put($cacheKey, self::implode(',', $likedMediaIds), 300);
        }
    }

    /**
     * @param  string  $mediaId
     * @param  string  $userId
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public static function checkMediaIsLikedByUser(string $mediaId, string $userId): bool
    {
        $cacheKey = self::getCollection().':creator:'.$userId;
        $likedMedias = Cache::store('octane')->get($cacheKey, false);
        return ($likedMedias !== false) ? in_array($mediaId, explode(',', $likedMedias)) :
            Redis::sismember($cacheKey, $mediaId);
    }
}
