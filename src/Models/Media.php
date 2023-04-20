<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Api\V1\Models\MediaComment;
use Aparlay\Core\Api\V1\Models\MediaLike;
use Aparlay\Core\Api\V1\Resources\SimpleUserTrait;
use Aparlay\Core\Casts\SimpleUserCast;
use Aparlay\Core\Database\Factories\MediaFactory;
use Aparlay\Core\Helpers\Cdn;
use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Helpers\NumberHelper;
use Aparlay\Core\Models\Enums\MediaContentGender;
use Aparlay\Core\Models\Enums\MediaSortCategories;
use Aparlay\Core\Models\Enums\MediaStatus;
use Aparlay\Core\Models\Enums\MediaVisibility;
use Aparlay\Core\Models\Scopes\MediaScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;
use MathPHP\Exception\BadDataException;
use MathPHP\Exception\OutOfBoundsException;
use MathPHP\Statistics\Descriptive;
use MathPHP\Statistics\Significance;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use Psr\SimpleCache\InvalidArgumentException;
use RedisException;

/**
 * Class Media.
 *
 * @property ObjectId $_id
 * @property Alert[]  $alertObjs
 * @property int      $comment_count
 * @property array    $comments
 * @property int      $content_gender
 * @property array    $count_fields_updated_at
 * @property string   $cover
 * @property Carbon   $created_at
 * @property ObjectId $created_by
 * @property array    $creator
 * @property User     $creatorObj
 * @property string   $description
 * @property string   $file
 * @property array    $files_history
 * @property array    $force_sort_positions
 * @property string   $hash
 * @property array    $hashtags
 * @property string   $image_blurred
 * @property bool     $is_protected
 * @property int      $length
 * @property int      $length_watched
 * @property int      $like_count
 * @property array    $likes
 * @property array    $links
 * @property string   $location
 * @property string   $mime_type
 * @property array    $people
 * @property array    $scores
 * @property int      $size
 * @property string   $slug
 * @property array    $sort_scores
 * @property int      $status
 * @property int      $tips
 * @property Carbon   $updated_at
 * @property User     $userObj
 * @property int      $visibility
 * @property int      $visit_count
 * @property array    $visits
 * @property int      $watched_count
 * @property-read string        $admin_url
 * @property-read int           $awesomeness_score
 * @property-read int           $beauty_score
 * @property-read int           $comment_score
 * @property-read string        $content_gender_label
 * @property-read string        $cover_file
 * @property-read string        $cover_url
 * @property-read string        $delete_prefix
 * @property-read string        $file_url
 * @property-read string        $image_blurred_url
 * @property-read bool          $is_image
 * @property-read bool          $is_private
 * @property-read bool          $is_processing_completed
 * @property-read bool          $is_video
 * @property-read int           $sent_tips
 * @property-read int           $skin_score
 * @property-read string        $slack_admin_url
 * @property-read string        $slack_subject_admin_url
 * @property-read int           $time_score
 * @property-read int           $visit_score
 *
 * @method static|self|Builder availableForFollower()
 * @method static|self|Builder availableForOwner()
 * @method static|self|Builder blockedFor(ObjectId|string $user)
 * @method static|self|Builder confirmed()
 * @method static|self|Builder creator(ObjectId|string $userId)
 * @method static|self|Builder explicit()
 * @method static|self|Builder genderContent(array|int $genderContent)
 * @method static|self|Builder hasForceSortPosition($category)
 * @method static|self|Builder hashtag(string $tag)
 * @method static|self|Builder hasNoForceSortPosition($category)
 * @method static|self|Builder licensed()
 * @method static|self|Builder media(ObjectId|string $userId)
 * @method static|self|Builder medias(ObjectId[] $mediaIds)
 * @method static|self|Builder notBlockedFor(ObjectId|string $user)
 * @method static|self|Builder notVisitedByDevice(string $deviceId)
 * @method static|self|Builder notVisitedByUserAndDevice(ObjectId|string $userId, string $deviceId)
 * @method static|self|Builder private()
 * @method static|self|Builder protected()
 * @method static|self|Builder public()
 * @method static|self|Builder slug(string $slug)
 * @method static|self|Builder sort(string $category)
 * @method static|self|Builder topless()
 * @method static|self|Builder user(ObjectId|string $userId)
 * @method static|self|Builder withoutExplicit()
 * @method static|self|Builder withoutTopless()
 */
class Media extends BaseModel
{
    use HasFactory;
    use Notifiable;
    use MediaScope;
    use SimpleUserTrait;
    use Searchable;

    /**
     * The collection associated with the model.
     *
     * @var string
     */
    protected $collection   = 'medias';
    protected string $path  = '';
    protected string $cover = '';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable     = [
        '_id',
        'description',
        'notes',
        'location',
        'hash',
        'file',
        'image_blurred',
        'files_history',
        'mime_type',
        'size',
        'length',
        'length_watched',
        'type',
        'like_count',
        'likes',
        'watched_count',
        'visit_count',
        'visits',
        'comment_count',
        'is_protected',
        'is_comments_enabled',
        'comments',
        'count_fields_updated_at',
        'visibility',
        'status',
        'tips',
        'is_music_licensed',
        'hashtags',
        'people',
        'processing_log',
        'blocked_user_ids',
        'creator',
        'scores',
        'sort_scores',
        'force_sort_positions',
        'slug',
        'tips',
        'content_gender',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];
    protected $attributes   = [
        'people' => [],
        'likes' => [],
        'visits' => [],
        'hashtags' => [],
        'scores' => [['type' => 'skin', 'score' => 0], ['type' => 'awesomeness', 'score' => 0], ['type' => 'beauty', 'score' => 0]],
        'is_protected' => false,
        'like_count' => 0,
        'watched_count' => 0,
        'visit_count' => 0,
        'comment_count' => 0,
        'tips' => 0,
        'content_gender' => 0,
        'sort_scores' => [
            'default' => 0,
            'guest' => 0,
            'returned' => 0,
            'registered' => 0,
            'paid' => 0,
        ],
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts        = [
        'creator' => SimpleUserCast::class . ':_id,username,avatar,is_liked,is_followed,is_verified',
        'status' => 'integer',
        'visibility' => 'integer',
        'like_count' => 'integer',
        'visit_count' => 'integer',
        'comment_count' => 'integer',
        'content_gender' => 'integer',
        'tips' => 'integer',
        'is_comments_enabled' => 'boolean',
        'image_blurred' => 'string',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden       = [
        'deleted_at',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends      = [];

    /**
     * Get the name of the index associated with the model.
     *
     * @return string
     */
    public function searchableAs()
    {
        return 'global';
    }

    /**
     * Determine if the model should be searchable.
     */
    public function shouldBeSearchable(): bool
    {
        return $this->visibility == MediaVisibility::PUBLIC->value && in_array($this->status, [MediaStatus::DENIED->value, MediaStatus::CONFIRMED->value]);
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        if (isset($this->content_gender)) {
            $gender = [MediaContentGender::from($this->content_gender)->label()];
        } else {
            $gender = [MediaContentGender::FEMALE->label()];
        }

        return [
            '_id' => (string) $this->_id,
            'type' => 'media',
            'poster' => $this->cover_url,
            'username' => $this->userObj->username      ?? '',
            'full_name' => $this->slug                  ?? '',
            'gender' => $gender,
            'description' => $this->description,
            'like_count' => $this->like_count,
            'visit_count' => $this->visit_count,
            'comment_count' => $this->comment_count,
            'hashtags' => $this->hashtags,
            'score' => $this->sort_scores['default'],
            'country' => $this->userObj->country_alpha2 ?? '',
            'is_adult' => $this->is_adult               ?? false,
            'skin_score' => $this->skin_score           ?? 5,
            'searchable' => implode(' ', $this->hashtags),
            'last_online_at' => 0,
            '_geo' => $this->userObj->last_location     ?? ['lat' => 0.0, 'lng' => 0.0],
        ];
    }

    public function searchIndexShouldBeUpdated(): bool
    {
        if ($this->isDirty([
            'cover_url',
            'content_gender',
            'description',
            'like_count',
            'visit_count',
            'comment_count',
            'hashtags',
            'sort_scores',
            'scores',
        ])) {
            return true;
        }

        return false;
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return MediaFactory::new();
    }

    /**
     * Get the phone associated with the user.
     */
    public function userObj()
    {
        return $this->belongsTo(User::class, 'creator._id');
    }

    /**
     * Get the phone associated with the user.
     */
    public function creatorObj()
    {
        return $this->belongsTo(User::class, 'creator._id');
    }

    /**
     * Get the phone associated with the user.
     */
    public function alertObjs()
    {
        return $this->hasMany(Alert::class, 'media_id');
    }

    /**
     * Get all the user's notifications.
     */
    public function userNotificationObjs()
    {
        return $this->morphMany(UserNotification::class, 'entity.');
    }

    /**
     * Get the user's full name.
     */
    public function getIsAdultAttribute(): bool
    {
        return $this->skin_score >= config('app.media.topless_skin_score', 8);
    }

    /**
     * Get the media's skin score.
     */
    public function getSkinScoreAttribute(): int
    {
        if (!empty($this->scores)) {
            foreach ($this->scores as $score) {
                if ('skin' === $score['type']) {
                    return (int) $score['score'];
                }
            }
        }

        return 0;
    }

    /**
     * Get the media's skin score.
     */
    public function getAwesomenessScoreAttribute(): int
    {
        if (!empty($this->scores)) {
            foreach ($this->scores as $score) {
                if ('awesomeness' === $score['type']) {
                    return (int) $score['score'];
                }
            }
        }

        return 0;
    }

    /**
     * Get the media's skin score.
     */
    public function getBeautyScoreAttribute(): int
    {
        if (!empty($this->scores)) {
            foreach ($this->scores as $score) {
                if ('beauty' === $score['type']) {
                    return (int) $score['score'];
                }
            }
        }

        return 0;
    }

    /**
     * Get the media's skin score.
     */
    public function getAlertsAttribute(): array|Collection
    {
        return Alert::query()->media($this->_id)->notVisited()->get();
    }

    /**
     * Get the media's skin score.
     */
    public function getTimeScoreAttribute(): int
    {
        $oldness = time() - $this->created_at->getTimestamp();

        return match (true) {
            $oldness <= 21600 => 10,
            $oldness <= 43200 => 9,
            $oldness <= 86400 => 8,
            $oldness <= 172800 => 7,
            $oldness <= 345600 => 6,
            $oldness <= 604800 => 5,
            $oldness <= 1209600 => 4,
            $oldness <= 2592000 => 3,
            $oldness <= 5184000 => 2,
            default => 1,
        };
    }

    /**
     * Get the media's skin score.
     *
     * @throws BadDataException
     * @throws OutOfBoundsException
     */
    public function getLikeScoreAttribute(): int
    {
        $timestamp      = $this->created_at->valueOf();
        $windowDuration = 86400 * 10;
        $startTime      = DT::timestampToUtc($timestamp - $windowDuration);
        $endTime        = DT::timestampToUtc($timestamp + $windowDuration);
        $meanLikes      = [];
        foreach (Analytic::query()->date($startTime, $endTime)->get() as $analytic) {
            if (isset($analytic['media']['mean_likes']) && 0 !== $analytic['media']['mean_likes']) {
                $meanLikes[] = $analytic['media']['mean_likes'];
            }
        }

        if (empty($meanLikes) || 0 === (int) $this->like_count) {
            return 0;
        }

        $sigma          = Descriptive::sd($meanLikes, true);

        if (0 == $sigma) {
            return 3;
        }

        $mean           = array_sum($meanLikes) / count($meanLikes);
        $z              = Significance::zScore($this->like_count, $mean, $sigma);

        return match (true) {
            $z >= 2.5 => 10,
            $z >= 2    && $z <= 2.5 => 9,
            $z >= 1.5  && $z <= 2 => 8,
            $z >= 1    && $z <= 1.5 => 7,
            $z >= 0.5  && $z <= 1 => 6,
            $z >= -0.5 && $z <= 0.5 => 5,
            $z >= -1   && $z <= -0.5 => 4,
            $z >= -1.5 && $z <= -1 => 3,
            $z >= -2   && $z <= -1.5 => 2,
            $z >= -2.5 && $z <= -2 => 1,
            default => 0,
        };
    }

    /**
     * Get the media visit score.
     */
    public function getVisitScoreAttribute(): int
    {
        $totalLengthWatched = self::availableForFollower()->sum('length_watched');
        $totalLength        = self::availableForFollower()->sum('length');

        $ratio              = $this->length > 0 ? $this->length_watched / $this->length : 0;

        if ($ratio && $totalLengthWatched && $totalLength) {
            $avgRatio = $totalLengthWatched / $totalLength;

            return $ratio > 0 ? (10 * ($ratio / ($ratio + $avgRatio))) : 0;
        }

        return 0;
    }

    /**
     * Get the user's full name.
     */
    public function getIsCompletedAttribute(): bool
    {
        return in_array(
            $this->status,
            [MediaStatus::COMPLETED->value, MediaStatus::CONFIRMED->value, MediaStatus::DENIED->value, MediaStatus::ADMIN_DELETED->value],
            true
        );
    }

    public function getSlackAdminUrlAttribute(): string
    {
        return "<{$this->admin_url}|video> By {$this->userObj->slack_admin_url}";
    }

    public function getAdminUrlAttribute(): string
    {
        return route('core.admin.media.view', ['media' => $this->_id]);
    }

    public function getFilenameAttribute(): string
    {
        return basename($this->file, '.' . pathinfo($this->file, PATHINFO_EXTENSION));
    }

    public function getDeletePrefixAttribute(): string
    {
        return substr(md5($this->file), 0, 5) . '_';
    }

    public function getCoverFileAttribute(): string
    {
        return $this->filename . '.jpg';
    }

    /**
     * Get the user's full name.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getIsFollowedAttribute(): bool
    {
        if (auth()->guest()) {
            return false;
        }

        $userId = auth()->user()->_id;

        return Follow::checkCreatorIsFollowedByUser((string) $this->creator['_id'], (string) $userId);
    }

    public function getIsPrivateAttribute(): bool
    {
        return $this->visibility === MediaVisibility::PRIVATE->value;
    }

    /**
     * Get the user's full name.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getIsLikedAttribute(): bool
    {
        if (auth()->guest()) {
            return false;
        }

        $userId   = auth()->user()->_id;
        $cacheKey = md5('media:' . $this->_id . ':likedBy:' . $userId);
        if (Cache::store('octane')->has($cacheKey)) {
            return Cache::store('octane')->get($cacheKey);
        }

        MediaLike::cacheByUserId($userId);
        $result   = MediaLike::checkMediaIsLikedByUser((string) $this->_id, (string) $userId);

        Cache::store('octane')->set($cacheKey, $result, config('app.cache.tenMinutes'));

        return $result;
    }

    /**
     * Get the user's full name.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getIsVisitedAttribute(): bool
    {
        if (auth()->guest()) {
            return false;
        }

        $userId = auth()->user()->_id;
        MediaVisit::cacheByUserId($userId);

        return MediaVisit::checkMediaIsVisitedByUser((string) $this->_id, (string) $userId);
    }

    public function getContentGenderLabelAttribute(): string
    {
        return in_array($this->content_gender, MediaContentGender::getAllValues()) ?
            MediaContentGender::from($this->content_gender)->label() :
            '';
    }

    /**
     * Get the user's full name.
     */
    public function getSentTipsAttribute(): int
    {
        $totalSentTips = 0;
        if (auth()->guest()) {
            return $totalSentTips;
        }

        $tipClass      = '\Aparlay\Payment\Models\Tip';
        if (class_exists($tipClass)) {
            $userId        = auth()->user()->_id;

            $tipClass::cacheByCreatorId($userId);
            $totalSentTips = $tipClass::totalSentTipsForMediaByUser((string) $this->_id, (string) $userId);
        }

        return $totalSentTips;
    }

    public function getCountFieldsUpdatedAtAttribute($attributeValue): mixed
    {
        foreach ($attributeValue as $field => $value) {
            /* MongoDB\BSON\UTCDateTime $value */
            $attributeValue[$field] = ($value instanceof UTCDateTime) ? $value->toDateTime()->getTimestamp() : $value;
        }

        return $attributeValue;
    }

    /**
     * @param mixed $attributeValue
     *
     * @return void
     */
    public function setCountFieldsUpdatedAtAttribute($attributeValue)
    {
        foreach ($attributeValue as $field => $value) {
            /* MongoDB\BSON\UTCDateTime $value */
            $attributeValue[$field] = ($value instanceof UTCDateTime) ? $value : DT::timestampToUtc($value);
        }

        $this->attributes['count_fields_updated_at'] = $attributeValue;
    }

    /**
     * Route notifications for the Slack channel.
     *
     * @param Notification $notification
     */
    public function routeNotificationForSlack($notification): string
    {
        return config('app.slack_webhook_url');
    }

    public static function getVisibilities(): array
    {
        return [
            MediaVisibility::PRIVATE->value => MediaVisibility::PRIVATE->label(),
            MediaVisibility::PUBLIC->value => MediaVisibility::PUBLIC->label(),
        ];
    }

    public static function getStatuses(): array
    {
        return [
            MediaStatus::QUEUED->value => MediaStatus::QUEUED->label(),
            MediaStatus::UPLOADED->value => MediaStatus::UPLOADED->label(),
            MediaStatus::IN_PROGRESS->value => MediaStatus::IN_PROGRESS->label(),
            MediaStatus::IN_REVIEW->value => MediaStatus::IN_REVIEW->label(),
            MediaStatus::COMPLETED->value => MediaStatus::COMPLETED->label(),
            MediaStatus::FAILED->value => MediaStatus::FAILED->label(),
            MediaStatus::DENIED->value => MediaStatus::DENIED->label(),
            MediaStatus::CONFIRMED->value => MediaStatus::CONFIRMED->label(),
            MediaStatus::ADMIN_DELETED->value => MediaStatus::ADMIN_DELETED->label(),
            MediaStatus::USER_DELETED->value => MediaStatus::USER_DELETED->label(),
            MediaStatus::USER_DELETED->value => MediaStatus::USER_DELETED->label(),
        ];
    }

    public function getIsProcessingCompletedAttribute(): bool
    {
        return $this->status === MediaStatus::COMPLETED->value;
    }

    public function getIsVideoAttribute(): bool
    {
        return Str::contains($this->mime_type, 'video');
    }

    public function getIsImageAttribute(): bool
    {
        return Str::contains($this->mime_type, 'image');
    }

    public function getFileUrlAttribute()
    {
        return Cdn::video($this->is_completed ? $this->file : 'default.mp4');
    }

    public function getCoverUrlAttribute()
    {
        return Cdn::cover($this->is_completed ? $this->filename . '.jpg' : 'default.jpg');
    }

    public function getImageBlurredUrlAttribute()
    {
        return Cdn::cover(($this->is_completed && !empty($this->image_blurred)) ? $this->image_blurred : 'default.jpg');
    }

    public function fileUrlFor(?User $user = null): string
    {
        if (!$this->is_private || !$this->isLockedFor($user)) {
            return $this->file_url;
        }

        return '';
    }

    public function coverUrlFor(?User $user = null): string
    {
        if (!$this->is_private || !$this->isLockedFor($user)) {
            return $this->cover_url;
        }

        return $this->image_blurred_url;
    }

    public function isLockedFor(?User $user = null): bool
    {
        // content is public no need check more
        if (!$this->is_private) {
            return false;
        }

        // content is private but no user no need check more
        if (is_null($user)) {
            return true;
        }

        // user is owner no need to check more
        if ((string) $user->_id !== (string) $this->creator['_id']) {
            return true;
        }

        return collect($user->subscriptions)->where('user_id', $this->creator['_id'])->first() === null;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function recalculateSortScores(): self
    {
        $sortScores        = [
            'default' => 0,
            'guest' => 0,
            'returned' => 0,
            'registered' => 0,
            'paid' => 0,
        ];
        foreach (MediaSortCategories::getAllValues() as $category) {
            $sortScores[$category] = $this->recalculateSortScoreByCategory($category);
        }

        $this->sort_scores = $sortScores;

        return $this;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function recalculateSortScoreByCategory(string $category): float
    {
        $config    = config('app.media.score_weights.' . $category);
        $cacheKey  = $this->getCollection() . ':promote:' . $this->_id;
        $promote   = (int) Redis::get($cacheKey);
        if ($this->created_at->getTimestamp() > Carbon::yesterday()->getTimestamp()) {
            $promote += match (true) {
                ($this->skin_score >= 9) => 1,
                ($this->skin_score >= 7) => 3,
                ($this->skin_score > 5) => 4,
                default => 2,
            };
        }

        $sortScore = ($this->awesomeness_score * (float) $config['awesomeness']) + ($this->beauty_score * (float) $config['beauty']) + ($this->skin_score * (float) $config['skin']) + ($promote * (float) $config['promote']);

        $sortScore += ($this->time_score * (float) $config['time']);
        $sortScore += ($this->like_score * (float) $config['like']);
        $sortScore += ($this->visit_score * (float) $config['visit']);

        return $sortScore;
    }

    public function updateLikes()
    {
        $likeCount                     = MediaLike::query()->media($this->_id)->count();
        $this->like_count              = $likeCount;
        $this->likes                   = MediaLike::query()
            ->media($this->_id)
            ->limit(10)
            ->recentFirst()
            ->get()
            ->filter(function (MediaLike $like) {
                return isset($like->creator) && !empty($like->creator['_id']);
            })
            ->map(function (MediaLike $like) {
                return [
                    '_id' => new ObjectId($like->creator['_id']),
                    'username' => $like->creator['username'],
                    'avatar' => $like->creator['avatar'],
                ];
            })
            ->toArray();
        $this->count_fields_updated_at = array_merge(
            $this->count_fields_updated_at,
            ['likes' => DT::utcNow()]
        );
        $this->save();
        $this->refresh();
    }

    public function updateVisits($duration = 0)
    {
        $length                        = ($duration > ($this->length * 3)) ? $this->length : $duration;
        $visitCount                    = MediaVisit::query()->media($this->_id)->count();
        $multiplier                    = config('app.media.visit_multiplier', 1);
        $this->length_watched += ($length * $multiplier);
        $this->watched_count           = $visitCount + $multiplier;
        $this->visits                  = MediaVisit::query()
            ->with('userObj')
            ->media($this->_id)
            ->limit(10)
            ->recentFirst()
            ->get()
            ->filter(function (MediaVisit $visit) {
                return !empty($visit->userObj);
            })
            ->map(function (MediaVisit $visit) {
                return [
                    '_id' => new ObjectId($visit->userObj->_id),
                    'username' => $visit->userObj->username,
                    'avatar' => $visit->userObj->avatar,
                ];
            })
            ->toArray();
        $this->count_fields_updated_at = array_merge(
            $this->count_fields_updated_at,
            ['visits' => DT::utcNow()]
        );
        $this->save();
        $this->refresh();

        $durationCacheKey              = 'tracking:media:duration:' . date('Y:m:d');
        $watchedCacheKey               = 'tracking:media:watched:' . date('Y:m:d');
        Redis::incrbyfloat($durationCacheKey, $length);
        Redis::incr($watchedCacheKey);
    }

    public function updateComments()
    {
        $commentCount                  = MediaComment::query()->media($this->_id)->count();
        $this->comment_count           = $commentCount;
        $this->comments                = MediaComment::query()
            ->media($this->_id)
            ->limit(10)
            ->recentFirst()
            ->get()
            ->filter(function (MediaComment $comment) {
                return isset($comment->creator) && !empty($comment->creator['_id']);
            })
            ->map(function (MediaComment $comment) {
                return [
                    '_id' => new ObjectId($comment->creator['_id']),
                    'username' => $comment->creator['username'],
                    'avatar' => $comment->creator['avatar'],
                ];
            })->toArray();
        $this->count_fields_updated_at = array_merge(
            $this->count_fields_updated_at,
            ['comments' => DT::utcNow()]
        );

        $this->save();
        $this->refresh();
    }

    public function likesNotificationMessage(): string
    {
        $mediaLikes    = [];
        foreach (MediaLike::query()->media($this->_id)->recent()->lazy() as $mediaLike) {
            if ((string) $mediaLike->creator['_id'] !== (string) $this->creator['_id']) {
                $mediaLikes[(string) $mediaLike->creator['_id']] = $mediaLike;
            }

            if (count($mediaLikes) > 2) {
                break;
            }
        }

        $mediaLikes    = array_values($mediaLikes);
        $twoUserExists = isset(
            $mediaLikes[0]->creatorObj->username,
            $mediaLikes[1]->creatorObj->username,
        );

        return match (true) {
            ($this->like_count > 2 && $twoUserExists) => __(
                ':username1, :username2 and :count others liked your video.',
                [
                    'username1' => $mediaLikes[0]->creatorObj->username,
                    'username2' => $mediaLikes[1]->creatorObj->username,
                    'count' => NumberHelper::shorten($this->like_count - 2),
                ]
            ),
            ($this->like_count === 2 && $twoUserExists) => __(
                ':username1 and :username2 liked your video.',
                [
                    'username1' => $mediaLikes[0]->creatorObj->username,
                    'username2' => $mediaLikes[1]->creatorObj->username,
                ]
            ),
            default => __(
                ':username liked your video.',
                ['username' => $mediaLikes[0]->creatorObj->username]
            ),
        };
    }

    public function commentsNotificationMessage(): string
    {
        $mediaComments = [];
        foreach (MediaComment::query()->with('creatorObj')->media($this->_id)->whereIdNeq($this->creator['_id'], 'creator._id')->recent()->lazy() as $mediaComment) {
            if ((string) $mediaComment->creator['_id'] !== (string) $this->creator['_id']) {
                $mediaComments[(string) $mediaComment->creator['_id']] = $mediaComment;
            }

            if (count($mediaComments) > 2) {
                break;
            }
        }

        $mediaComments = array_values($mediaComments);
        $twoUserExists = isset(
            $mediaComments[0]->creatorObj->username,
            $mediaComments[1]->creatorObj->username
        );

        return match (true) {
            ($this->comment_count > 2 && $twoUserExists) => __(
                ':username1, :username2 and :count others commented on your video.',
                [
                    'username1' => $mediaComments[0]->creatorObj->username,
                    'username2' => $mediaComments[1]->creatorObj->username,
                    'count' => NumberHelper::shorten($this->comment_count - 2),
                ]
            ),
            ($this->comment_count === 2 && $twoUserExists) => __(
                ':username1 and :username2 commented on your video.',
                [
                    'username1' => $mediaComments[0]->creatorObj->username,
                    'username2' => $mediaComments[1]->creatorObj->username,
                ]
            ),
            default => __(
                ':username commented on your video.',
                ['username' => $mediaComments[0]->creatorObj->username]
            ),
        };
    }

    /**
     * @throws RedisException
     */
    private function cacheInPublicToplessMediaIds(): void
    {
        $toplessMediaIdsCacheKey = (new self())->getCollection() . ':topless:ids';
        foreach ($this->sort_scores as $category => $score) {
            Redis::zAdd($toplessMediaIdsCacheKey . ':' . $category, $this->sort_scores[$category], (string) $this->_id);
        }
    }

    /**
     * @throws RedisException
     */
    private function cacheInPublicExplicitMediaIds(): void
    {
        $explicitMediaIdsCacheKey = (new self())->getCollection() . ':explicit:ids';
        foreach ($this->sort_scores as $category => $score) {
            Redis::zAdd($explicitMediaIdsCacheKey . ':' . $category, $this->sort_scores[$category], (string) $this->_id);
        }
    }

    /**
     * @throws RedisException
     */
    private function cacheInPublicMediaIds(): void
    {
        $cacheKey = (new self())->getCollection() . ':ids';
        foreach ($this->sort_scores as $category => $score) {
            if (is_numeric($this->sort_scores[$category])) {
                Redis::zAdd($cacheKey . ':' . $category, $this->sort_scores[$category], (string) $this->_id);
            }
        }
    }

    /**
     * @throws RedisException
     */
    private function cacheInFemaleContentMediaIds(): void
    {
        if ($this->content_gender === MediaContentGender::FEMALE->value) {
            $cacheKey = (new self())->getCollection() . ':ids:female';
            Redis::zAdd($cacheKey, 0, (string) $this->_id);
        }
    }

    /**
     * @throws RedisException
     */
    private function cacheInMaleContentMediaIds(): void
    {
        if ($this->content_gender === MediaContentGender::MALE->value) {
            $cacheKey = (new self())->getCollection() . ':ids:male';
            Redis::zAdd($cacheKey, 0, (string) $this->_id);
        }
    }

    /**
     * @throws RedisException
     */
    private function cacheInTransgenderContentMediaIds(): void
    {
        if ($this->content_gender === MediaContentGender::TRANSGENDER->value) {
            $cacheKey = (new self())->getCollection() . ':ids:transgender';
            Redis::zAdd($cacheKey, 0, (string) $this->_id);
        }
    }

    /**
     * @throws RedisException
     */
    public function storeInGeneralCaches(): self
    {
        if ($this->status !== MediaStatus::CONFIRMED->value || $this->visibility !== MediaVisibility::PUBLIC->value) {
            return $this;
        }

        $this->cacheInPublicMediaIds();

        if ($this->skin_score >= config('app.media.topless_skin_score')) {
            $this->cacheInPublicToplessMediaIds();
        }
        if ($this->skin_score >= config('app.media.explicit_skin_score')) {
            $this->cacheInPublicExplicitMediaIds();
        }

        $this->cacheInFemaleContentMediaIds();
        $this->cacheInMaleContentMediaIds();
        $this->cacheInTransgenderContentMediaIds();

        return $this;
    }
}
