<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Api\V1\Models\Alert;
use Aparlay\Core\Api\V1\Models\MediaLike;
use Aparlay\Core\Api\V1\Models\MediaVisit;
use Aparlay\Core\Api\V1\Resources\SimpleUserTrait;
use Aparlay\Core\Casts\SimpleUserCast;
use Aparlay\Core\Database\Factories\MediaFactory;
use Aparlay\Core\Helpers\Cdn;
use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Models\Enums\MediaStatus;
use Aparlay\Core\Models\Enums\MediaVisibility;
use Aparlay\Core\Models\Scopes\MediaScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Redis;
use Laravel\Scout\Searchable;
use MathPHP\Exception\BadDataException;
use MathPHP\Exception\OutOfBoundsException;
use MathPHP\Statistics\Descriptive;
use MathPHP\Statistics\Significance;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

/**
 * Class Media.
 *
 * @property ObjectId    $_id
 * @property ObjectId    $user_id
 * @property string      $description
 * @property string      $location
 * @property string      $hash
 * @property string      $file
 * @property string      $mime_type
 * @property int         $size
 * @property int         $length
 * @property int         $visibility
 * @property int         $like_count
 * @property int         $comment_count
 * @property array       $count_fields_updated_at
 * @property array       $likes
 * @property array       $comments
 * @property int         $status
 * @property int         $tips
 * @property array       $hashtags
 * @property array       $people
 * @property array       $creator
 * @property string      $cover
 * @property string      $slug
 * @property ObjectId    $created_by
 * @property UTCDateTime $created_at
 * @property UTCDateTime $updated_at
 * @property mixed       $filename
 * @property array       $links
 * @property bool        $is_protected
 * @property array       $scores
 * @property float       $sort_score
 * @property User        $userObj
 * @property Alert[]     $alertObjs
 *
 * @property-read string $slack_subject_admin_url
 * @property-read string $slack_admin_url
 * @property-read string $cover_url
 * @property-read string $file_url
 * @property-read int $skin_score
 * @property-read int $sent_tips
 *
 *
 * @method static |self|Builder creator(ObjectId|string $userId) get creator user
 * @method static |self|Builder user(ObjectId|string $userId)    get blocked user
 * @method static |self|Builder availableForFollower()    get available content for followers
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
    protected $collection = 'medias';

    protected string $path = '';

    protected string $cover = '';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        '_id',
        'description',
        'notes',
        'location',
        'hash',
        'file',
        'files_history',
        'mime_type',
        'size',
        'length',
        'length_watched',
        'type',
        'like_count',
        'likes',
        'visit_count',
        'visits',
        'comment_count',
        'is_protected',
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
        'sort_score',
        'slug',
        'tips',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    protected $attributes = [
        'people' => [],
        'likes' => [],
        'visits' => [],
        'scores' => [['type' => 'skin', 'score' => 0], ['type' => 'awesomeness', 'score' => 0]],
        'is_protected' => false,
        'like_count' => 0,
        'visit_count' => 0,
        'comment_count' => 0,
        'tips' => 0,
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'creator' => SimpleUserCast::class.':is_followed',
        'status' => 'integer',
        'visibility' => 'integer',
        'like_count' => 'integer',
        'visit_count' => 'integer',
        'comment_count' => 'integer',
        'tips' => 'integer',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'deleted_at',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [];

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
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        return [
            '_id' => (string) $this->_id,
            'type' => 'media',
            'poster' => $this->cover_url,
            'username' => $this->userObj->username,
            'full_name' => $this->userObj->full_name,
            'description' => $this->description,
            'hashtags' => $this->hashtags,
            'score' => $this->sort_score,
            'country' => $this->userObj->country_alpha2,
            '_geo' => $this->userObj->last_location ?? ['lat' => 0.0, 'lng' => 0.0],
        ];
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
     * Get the user's full name.
     */
    public function getIsAdultAttribute(): bool
    {
        return $this->skin_score >= 8;
    }

    /**
     * Get the media's skin score.
     */
    public function getSkinScoreAttribute(): int
    {
        if (! empty($this->scores)) {
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
        if (! empty($this->scores)) {
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
     *
     * @return array|Collection
     */
    public function getAlertsAttribute(): array|Collection
    {
        return Alert::media($this->_id)->notVisited()->get();
    }

    /**
     * Get the media's skin score.
     */
    public function getTimeScoreAttribute(): int
    {
        $oldness = time() - $this->created_at->valueOf();

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
        $timestamp = $this->created_at->valueOf();
        $windowDuration = 86400 * 10;
        $startTime = DT::timestampToUtc($timestamp - $windowDuration);
        $endTime = DT::timestampToUtc($timestamp + $windowDuration);
        $meanLikes = [];
        foreach (Analytic::date($startTime, $endTime)->get() as $analytic) {
            if (isset($analytic['media']['mean_likes']) && 0 !== $analytic['media']['mean_likes']) {
                $meanLikes[] = $analytic['media']['mean_likes'];
            }
        }

        if (empty($meanLikes) || 0 === (int) $this->like_count) {
            return 0;
        }

        $sigma = Descriptive::sd($meanLikes, true);

        if (0 == $sigma) {
            return 3;
        }

        $mean = array_sum($meanLikes) / count($meanLikes);
        $z = Significance::zScore($this->like_count, $mean, $sigma);

        return match (true) {
            $z >= 2.5 => 10,
            $z >= 2 && $z <= 2.5 => 9,
            $z >= 1.5 && $z <= 2 => 8,
            $z >= 1 && $z <= 1.5 => 7,
            $z >= 0.5 && $z <= 1 => 6,
            $z >= -0.5 && $z <= 0.5 => 5,
            $z >= -1 && $z <= -0.5 => 4,
            $z >= -1.5 && $z <= -1 => 3,
            $z >= -2 && $z <= -1.5 => 2,
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
        $totalLength = self::availableForFollower()->sum('length');

        $ratio = $this->length > 0 ? $this->length_watched / $this->length : 0;

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
            [MediaStatus::COMPLETED->value, MediaStatus::CONFIRMED->value, MediaStatus::ADMIN_DELETED->value],
            true
        );
    }

    /**
     * @return string
     */
    public function getSlackAdminUrlAttribute(): string
    {
        return "<{$this->admin_url}|video> By {$this->userObj->slack_admin_url}";
    }

    /**
     * @return string
     */
    public function getAdminUrlAttribute(): string
    {
        return route('core.admin.media.view', ['media' => $this->_id]);
    }

    /**
     * @return string
     */
    public function getFilenameAttribute(): string
    {
        return basename($this->file, '.'.pathinfo($this->file, PATHINFO_EXTENSION));
    }

    /**
     * Get the user's full name.
     */
    public function getIsFollowedAttribute(): bool
    {
        if (auth()->guest()) {
            return false;
        }

        $cacheKey = (new Follow())->getCollection().':creator:'.auth()->user()->_id;
        Follow::cacheByUserId(auth()->user()->_id);

        return Redis::sismember($cacheKey, (string) $this->creator['_id']);
    }

    /**
     * Get the user's full name.
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getIsLikedAttribute(): bool
    {
        if (auth()->guest()) {
            return false;
        }

        $userId = auth()->user()->_id;
        MediaLike::cacheByUserId($userId);

        return MediaLike::checkMediaIsLikedByUser($this->_id, $userId);
    }

    /**
     * Get the user's full name.
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

    /**
     * Get the user's full name.
     */
    public function getSentTipsAttribute(): int
    {
        $totalSentTips = 0;
        if (auth()->guest()) {
            return $totalSentTips;
        }

        $tipClass = '\Aparlay\Payment\Models\Tip';
        if (class_exists($tipClass)) {
            $mediaTipCacheKey = (new $tipClass())->getCollection().':creator:'.auth()->user()->_id;

            $tipClass::cacheByCreatorId(auth()->user()->_id);
            $totalSentTips = Redis::hGet($mediaTipCacheKey, (string) $this->_id);
            $totalSentTips = $totalSentTips !== false ? (int) $totalSentTips : 0;
        }

        return $totalSentTips;
    }

    /**
     * @param $attributeValue
     * @return mixed
     */
    public function getCountFieldsUpdatedAtAttribute($attributeValue): mixed
    {
        foreach ($attributeValue as $field => $value) {
            /* MongoDB\BSON\UTCDateTime $value */
            $attributeValue[$field] = ($value instanceof UTCDateTime) ? $value->toDateTime()->getTimestamp() : $value;
        }

        return $attributeValue;
    }

    /**
     * @param $attributeValue
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
     *
     * @return string
     */
    public function routeNotificationForSlack($notification): string
    {
        return config('app.slack_webhook_url');
    }

    /**
     * @return array
     */
    public static function getVisibilities(): array
    {
        return [
            MediaVisibility::PRIVATE->value => MediaVisibility::PRIVATE->label(),
            MediaVisibility::PUBLIC->value => MediaVisibility::PUBLIC->label(),
        ];
    }

    /**
     * @return array
     */
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

    public function getFileUrlAttribute()
    {
        return Cdn::video($this->is_completed ? $this->file : 'default.mp4');
    }

    public function getCoverUrlAttribute()
    {
        return Cdn::cover($this->is_completed ? $this->filename.'.jpg' : 'default.jpg');
    }
}
