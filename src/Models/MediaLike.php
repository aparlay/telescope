<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Casts\SimpleUserCast;
use Aparlay\Core\Database\Factories\MediaLikeFactory;
use Aparlay\Core\Models\Queries\MediaLikeQueryBuilder;
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
 */
class MediaLike extends BaseModel
{
    use HasFactory;
    use Notifiable;

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
        'creator' => SimpleUserCast::class.':_id,username,avatar,is_liked,is_followed,is_verified',
    ];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return MediaLikeFactory::new();
    }

    /**
     * @return MediaLikeQueryBuilder|Builder
     */
    public static function query(): MediaLikeQueryBuilder|Builder
    {
        return parent::query();
    }

    /**
     * @param $query
     * @return MediaLikeQueryBuilder
     */
    public function newEloquentBuilder($query): MediaLikeQueryBuilder
    {
        return new MediaLikeQueryBuilder($query);
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
     * @param  bool  $refresh
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public static function cacheByUserId(ObjectId | string $userId, bool $refresh = false): void
    {
        $userId = $userId instanceof ObjectId ? (string) $userId : $userId;
        $cacheKey = (new self())->getCollection().':creator:'.$userId;

        if ($refresh) {
            Redis::del($cacheKey);
            Cache::store('octane')->forget($cacheKey);
        }

        if (Cache::store('octane')->get($cacheKey, false) !== false) {
            return; // cache already exists
        }

        if (! Redis::exists($cacheKey)) {
            $likedMediaIds = self::project(['media_id' => true, '_id' => false])
                ->creator($userId)
                ->pluck('media_id')
                ->toArray();

            if (empty($likedMediaIds)) {
                $likedMediaIds = [''];
            }

            Cache::store('octane')->put($cacheKey, implode(',', $likedMediaIds), 300);

            Redis::sAdd($cacheKey, ...$likedMediaIds);
            Redis::expire($cacheKey, config('app.cache.veryLongDuration'));
        }

        if (Cache::store('octane')->get($cacheKey, false) === false) {
            $likedMediaIds = Redis::sMembers($cacheKey);

            Cache::store('octane')->put($cacheKey, implode(',', $likedMediaIds), 300);
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
        $cacheKey = (new self())->getCollection().':creator:'.$userId;
        $likedMedias = Cache::store('octane')->get($cacheKey, false);

        return ($likedMedias !== false) ? in_array($mediaId, explode(',', $likedMedias)) :
            Redis::sismember($cacheKey, $mediaId);
    }
}
