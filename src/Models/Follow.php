<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Casts\SimpleUserCast;
use Aparlay\Core\Database\Factories\FollowFactory;
use Aparlay\Core\Models\Enums\FollowStatus;
use Aparlay\Core\Models\Scopes\FollowScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use MongoDB\BSON\ObjectId;

/**
 * Class Follow.
 *
 * @property ObjectId   $_id
 * @property int        $status
 * @property array      $user
 * @property array      $creator
 * @property bool       $is_deleted
 * @property string     $created_at
 * @property User       $creatorObj
 * @property User       $userObj
 * @property mixed|null $creator_id
 * @property mixed|null $user_id
 * @property string     $aliasModel
 *
 * @method static |self|Builder creator(ObjectId|string $userId) get creator user
 * @method static |self|Builder user(ObjectId|string $userId)    get blocked user
 */
class Follow extends BaseModel
{
    use HasFactory;
    use Notifiable;
    use FollowScope;

    /**
     * The collection associated with the model.
     *
     * @var string
     */
    protected $collection = 'user_follows';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        '_id',
        'user',
        'creator',
        'is_deleted',
        'status',
        'created_at',
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
        'creator' => SimpleUserCast::class.':_id,username,avatar,is_liked,is_followed,is_verified',
        'user' => SimpleUserCast::class.':_id,username,avatar,is_liked,is_followed,is_verified',
        'is_deleted' => 'boolean',
        'status' => 'integer',
    ];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return FollowFactory::new();
    }

    /**
     * Get the user associated with the follow.
     */
    public function userObj()
    {
        return $this->belongsTo(User::class, 'user._id');
    }

    /**
     * Get the creator associated with the follow.
     */
    public function creatorObj()
    {
        return $this->belongsTo(User::class, 'creator._id');
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
            $followerIds = self::project(['user._id' => true, '_id' => false])
                ->creator(new ObjectId($userId))
                ->pluck('user._id')
                ->toArray();

            if (empty($followerIds)) {
                $followerIds = [''];
            }

            $followerIds = array_map('strval', $followerIds);

            Cache::store('octane')->put($cacheKey, implode(',', $followerIds), 300);

            Redis::sAdd($cacheKey, ...$followerIds);
            Redis::expire($cacheKey, config('app.cache.veryLongDuration'));
        }

        if (Cache::store('octane')->get($cacheKey, false) === false) {
            $followerIds = Redis::sMembers($cacheKey);

            Cache::store('octane')->put($cacheKey, implode(',', $followerIds), 300);
        }
    }

    /**
     * @param  string  $creatorId
     * @param  string  $userId
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public static function checkCreatorIsFollowedByUser(string $creatorId, string $userId): bool
    {
        $cacheKey = (new self())->getCollection().':creator:'.$userId;
        $followerIds = Cache::store('octane')->get($cacheKey, false);

        return ($followerIds !== false) ? in_array($creatorId, explode(',', $followerIds)) :
            Redis::sismember($cacheKey, (string) $creatorId);
    }

    /**
     * @return array
     */
    public static function getStatuses(): array
    {
        return [
            FollowStatus::PENDING->value => FollowStatus::PENDING->label(),
            FollowStatus::ACCEPTED->value => FollowStatus::ACCEPTED->label(),
        ];
    }
}
