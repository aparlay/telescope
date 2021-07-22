<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Api\V1\Models\Alert;
use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Database\Factories\MediaFactory;
use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Models\Scopes\MediaScope;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Jenssegers\Mongodb\Eloquent\Model;
use MathPHP\Exception\BadDataException;
use MathPHP\Exception\OutOfBoundsException;
use MathPHP\Statistics\Descriptive;
use MathPHP\Statistics\Significance;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

/**
 * Class Media
 *
 * @package Aparlay\Core\Models
 * @property ObjectId $_id
 * @property string $description
 * @property string $location
 * @property string $hash
 * @property string $file
 * @property string $mime_type
 * @property int $size
 * @property int $length
 * @property int $visibility
 * @property int $like_count
 * @property int $comment_count
 * @property array $count_fields_updated_at
 * @property array $likes
 * @property array $comments
 * @property int $status
 * @property array $hashtags
 * @property array $people
 * @property array $creator
 * @property string $cover
 * @property string $slug
 * @property ObjectId $created_by
 * @property UTCDateTime $created_at
 * @property UTCDateTime $updated_at
 * @property-read mixed $filename
 * @property-read array $links
 * @property-read bool $is_protected
 *
 * @OA\Schema()
 */
class Media extends Model
{
    use HasFactory;
    use Notifiable;
    use MediaScope;

    public const VISIBILITY_PUBLIC = 1;
    public const VISIBILITY_PRIVATE = 0;

    public const STATUS_QUEUED = 0;
    public const STATUS_UPLOADED = 1;
    public const STATUS_IN_PROGRESS = 2;
    public const STATUS_COMPLETED = 3;
    public const STATUS_FAILED = 4;
    public const STATUS_CONFIRMED = 5;
    public const STATUS_DENIED = 6;
    public const STATUS_IN_REVIEW = 7;
    public const STATUS_ADMIN_DELETED = 9;
    public const STATUS_USER_DELETED = 10;

    /**
     * The collection associated with the model.
     * @var string
     */
    protected $collection = 'medias';

    /**
     * @var string
     */
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
        'comments',
        'count_fields_updated_at',
        'visibility',
        'status',
        'is_music_licensed',
        'hashtags',
        'people',
        'processing_log',
        'blocked_user_ids',
        'creator',
        'scores',
        'sort_score',
        'slug',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
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
    protected $appends = ['cover', 'is_liked', 'is_visited', 'is_adult', 'alerts', '_links'];


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        '_id' => 'string',
        'email_verified_at' => 'datetime',
        'phone_number_verified_at' => 'datetime',
        'count_fields_updated_at' => 'timestamp',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
        'deleted_at' => 'timestamp',
    ];

    /**
     * Set the media's creator.
     *
     * @return array
     */
    public function setCreatorAttribute($creator)
    {
        $creator = User::user($creator['_id'])->first();

        return ['_id' => $creator->_id, 'username' => $creator->username, 'avatar' => $creator->avatar];
    }

    /**
     * Get the user's full name.
     *
     * @return bool
     */
    public function getIsAdultAttribute(): bool
    {
        return $this->skin_score >= 8;
    }

    /**
     * Get the media's skin score.
     *
     * @return int
     */
    public function getSkinScoreAttribute(): int
    {
        if (! empty($this->scores)) {
            foreach ($this->scores as $score) {
                if ($score['type'] === 'skin') {
                    return $score['score'];
                }
            }
        }

        return 0;
    }

    /**
     * Get the media's skin score.
     *
     * @return int
     */
    public function getAwesomenessScoreAttribute(): int
    {
        if (! empty($this->scores)) {
            foreach ($this->scores as $score) {
                if ($score['type'] === 'awesomeness') {
                    return $score['score'];
                }
            }
        }

        return 0;
    }

    /**
     * Get the media's skin score.
     *
     * @return array
     */
    public function getAlertsAttribute(): array
    {
        if (! isset($this->creator['_id']) || auth()->guest() ||
            ((string)$this->creator['_id'] !== (string)auth()->user()->_id)) {
            return [];
        }

        $result = [];
        foreach (Alert::media($this->_id)->notVisited()->all() as $alert) {
            $result[] = $alert->toArray(['_id', 'title', 'reason', 'created_at']);
        }

        return $result;
    }

    /**
     * Get the media's skin score.
     *
     * @return int
     */
    public function getTimeScoreAttribute(): int
    {
        $oldness = time() - DT::utcToTimestamp($this->created_at);

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
     * @return int
     * @throws BadDataException
     * @throws OutOfBoundsException
     */
    public function getLikeScoreAttribute(): int
    {
        $timestamp = DT::utcToTimestamp($this->created_at);
        $windowDuration = 86400 * 10;
        $startUtc = DT::timestampToUtc($timestamp - $windowDuration);
        $endUtc = DT::timestampToUtc($timestamp + $windowDuration);
        $meanLikes = [];
        foreach (Analytic::date($startUtc, $endUtc)->get() as $analytic) {
            if (isset($analytic['media']['mean_likes']) && $analytic['media']['mean_likes'] !== 0) {
                $meanLikes[] = $analytic['media']['mean_likes'];
            }
        }

        if (empty($meanLikes) || (int)$this->like_count === 0) {
            return 0;
        }

        $sigma = Descriptive::sd($meanLikes, true);

        if ($sigma == 0) {
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
     * Get the user's full name.
     *
     * @return string
     */
    public function getCoverAttribute($value)
    {
        return config('cdn.covers') . ($this->is_completed ? $this->filename . '.jpg' : 'default.jpg');
    }

    /**
     * Get the user's full name.
     *
     * @return bool
     */
    public function getIsCompletedAttribute(): bool
    {
        return in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_CONFIRMED, self::STATUS_ADMIN_DELETED], true);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function setSlugAttribute(): string
    {
        return $this->attributes['slug'] = $this->generateSlug(6);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getLinksAttribute(): array
    {
        return [
            'self' => route('media.show', ['media' => $this]),
            'index' => route('user.media_list', ['user' => User::user($this->created_by)->first()]),
        ];
    }

    /**
     * @param int $length
     * @return string
     * @throws Exception
     */
    public function generateSlug(int $length): string
    {
        $slug = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);

        return (self::slug($slug)->first() === null) ? $slug : $this->generateSlug($length);
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return Factory
     */
    protected static function newFactory(): Factory
    {
        return MediaFactory::new();
    }

    public static function getVisibilities()
    {
        return [
            self::VISIBILITY_PRIVATE => 'Private',
            self::VISIBILITY_PUBLIC => 'Public',
        ];
    }

    public static function getStatuses()
    {
        return [
            self::STATUS_QUEUED => 'Queued',
            self::STATUS_UPLOADED => 'Uploaded',
            self::STATUS_IN_PROGRESS => 'In-Progress',
            self::STATUS_COMPLETED => 'Waiting For Review',
            self::STATUS_FAILED => 'Failed',
            self::STATUS_CONFIRMED => 'Confirmed',
            self::STATUS_DENIED => 'Denied',
            self::STATUS_ADMIN_DELETED => 'Deleted By Admin',
            self::STATUS_USER_DELETED => 'Deleted',
            self::STATUS_IN_REVIEW => 'Under review',
        ];
    }
}
