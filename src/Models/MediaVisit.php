<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Database\Factories\MediaVisitFactory;
use Aparlay\Core\Models\Queries\MediaVisitQueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Redis;
use Jenssegers\Mongodb\Relations\BelongsTo;
use MongoDB\BSON\ObjectId;

/**
 * Class MediaVisit.
 *
 * @property ObjectId $_id
 * @property string   $aliasModel
 * @property string   $created_at
 * @property string   $date
 * @property array    $media_ids
 * @property-read Media $mediaObj
 * @property-read User $userObj
 * @property ObjectId $user_id
 */
class MediaVisit extends BaseModel
{
    use HasFactory;
    use Notifiable;
    public $media_id;
    public $duration;

    /**
     * The collection associated with the model.
     *
     * @var string
     */
    protected $collection = 'media_visits';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable   = [
        '_id',
        'user_id',
        'media_ids',
        'date',
        'created_at',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden     = [
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts      = [
    ];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return MediaVisitFactory::new();
    }

    public static function query(): MediaVisitQueryBuilder|Builder
    {
        return parent::query();
    }

    public function newEloquentBuilder($query): MediaVisitQueryBuilder
    {
        return new MediaVisitQueryBuilder($query);
    }

    public function userObj(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function mediaObj(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'media_id');
    }

    /**
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public static function cacheByUserId(ObjectId|string $userId, bool $refresh = false): void
    {
        $userId   = $userId instanceof ObjectId ? (string) $userId : $userId;
        $cacheKey = (new self())->getCollection() . ':creator:' . $userId;

        if ($refresh) {
            Redis::del($cacheKey);
        }

        if (!Redis::exists($cacheKey)) {
            $visitedMediaIds = self::project(['media_ids' => true, '_id' => false])
                ->user($userId)
                ->pluck('media_ids')
                ->toArray();

            if (empty($visitedMediaIds)) {
                $visitedMediaIds = [''];
            }
            $visitedMediaIds = Arr::flatten($visitedMediaIds);
            $visitedMediaIds = array_map('strval', $visitedMediaIds);

            Redis::sAdd($cacheKey, ...$visitedMediaIds);
            Redis::expire($cacheKey, config('app.cache.veryLongDuration'));
        }
    }

    /**
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public static function checkMediaIsVisitedByUser(string $mediaId, string $userId): bool
    {
        $cacheKey = (new self())->getCollection() . ':creator:' . $userId;

        return (bool) Redis::sismember($cacheKey, $mediaId);
    }
}
