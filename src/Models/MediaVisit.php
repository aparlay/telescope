<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Database\Factories\MediaVisitFactory;
use Aparlay\Core\Models\Scopes\MediaVisitScope;
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
 * @property string $date
 * @property ObjectId $user_id
 * @property array $media_ids
 * @property string $created_at
 *
 * @property-read User $userObj
 * @property-read Media $mediaObj
 * @property string $aliasModel
 *
 * @method static |self|Builder media(ObjectId $mediaId)    get media visits for thew given media
 * @method static |self|Builder user(ObjectId $userId)      get media visits for thew given user
 * @method static |self|Builder date(string $date)          get media visits for thew given date
 */
class MediaVisit extends BaseModel
{
    use HasFactory;
    use Notifiable;
    use MediaVisitScope;

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
    protected $fillable = [
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
        return MediaVisitFactory::new();
    }

    /**
     * @return BelongsTo
     */
    public function userObj(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsTo
     */
    public function mediaObj(): BelongsTo
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
            $visitedMediaIds = MediaVisit::creator($userId)
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
}
