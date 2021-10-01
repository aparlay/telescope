<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Database\Factories\MediaLikeFactory;
use Aparlay\Core\Models\Casts\SimpleUser;
use Aparlay\Core\Models\Scopes\MediaLikeScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
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
     * @param ObjectId|string $userId
     */
    public static function cacheByUserId(ObjectId | string $userId): void
    {
        $userId = $userId instanceof ObjectId ? (string) $userId : $userId;
        $cacheKey = (new self())->getCollection().':creator:'.$userId;

        if (! Redis::exists($cacheKey)) {
            $likedMediaIds = self::project(['media_id' => true, '_id' => false])
                ->creator(new ObjectId($userId))
                ->pluck('media_id')
                ->toArray();

            if (empty($likedMediaIds)) {
                $likedMediaIds = [''];
            }

            $likedMediaIds = array_map('strval', $likedMediaIds);

            Redis::sAdd($cacheKey, ...$likedMediaIds);
            Redis::expire($cacheKey, config('app.cache.veryLongDuration'));
        }
    }
}
