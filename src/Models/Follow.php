<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Casts\SimpleUserCast;
use Aparlay\Core\Database\Factories\FollowFactory;
use Aparlay\Core\Models\Enums\FollowStatus;
use Aparlay\Core\Models\Queries\FollowQueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use MongoDB\BSON\ObjectId;
use Psr\SimpleCache\InvalidArgumentException;

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
 */
class Follow extends BaseModel
{
    use HasFactory;
    use Notifiable;

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
     * @return FollowQueryBuilder|Builder
     */
    public static function query(): FollowQueryBuilder|Builder
    {
        return parent::query();
    }

    /**
     * @param $query
     * @return FollowQueryBuilder
     */
    public function newEloquentBuilder($query): FollowQueryBuilder
    {
        return new FollowQueryBuilder($query);
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
     * @throws InvalidArgumentException
     */
    public static function cacheByUserId(ObjectId | string $userId, bool $refresh = false): void
    {
        $userId = $userId instanceof ObjectId ? (string) $userId : $userId;
        $cacheKey = (new self())->getCollection().':creator:'.$userId;

        if ($refresh) {
            Redis::del($cacheKey);
        }

        if (! Redis::exists($cacheKey)) {
            $followingIds = self::project(['user._id' => true, '_id' => false])
                ->creator(new ObjectId($userId))
                ->pluck('user._id')
                ->toArray();

            if (empty($followingIds)) {
                $followingIds = [''];
            }

            $followingIds = array_map('strval', $followingIds);

            Redis::sAdd($cacheKey, ...$followingIds);
            Redis::expire($cacheKey, config('app.cache.veryLongDuration'));
        }
    }

    /**
     * @param  string  $creatorId
     * @param  string  $userId
     * @return bool
     * @throws InvalidArgumentException
     */
    public static function checkCreatorIsFollowedByUser(string $creatorId, string $userId): bool
    {
        $octaneCacheKey = 'user:'.$userId.':followedBy:'.$creatorId;
        if (Cache::store('octane')->has($octaneCacheKey)) {
            return Cache::store('octane')->get($octaneCacheKey);
        }

        self::cacheByUserId($userId);

        $result = Redis::sismember((new self())->getCollection().':creator:'.$userId, $creatorId);
        Cache::store('octane')->set($octaneCacheKey, $result ? '1' : '0', 300);

        return $result;
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
