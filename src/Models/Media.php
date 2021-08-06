<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Api\V1\Models\Follow;
use Aparlay\Core\Api\V1\Resources\SimpleUserTrait;
use Aparlay\Core\Database\Factories\MediaFactory;
use Aparlay\Core\Helpers\Cdn;
use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Jobs\DeleteMediaLike;
use Aparlay\Core\Jobs\UploadMedia;
use Aparlay\Core\Models\Scopes\MediaScope;
use Exception;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
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
 *
 * @OA\Schema()
 *
 * @method static |self|Builder creator(ObjectId|string $userId) get creator user
 * @method static |self|Builder user(ObjectId|string $userId)    get blocked user
 */
class Media extends Model
{
    use HasFactory;
    use Notifiable;
    use MediaScope;
    use SimpleUserTrait;

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

    protected $attributes = [
        'people' => [],
        'likes' => [],
        'visits' => [],
        'status' => self::STATUS_QUEUED,
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
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($media) {
            $media->parseDescription();
            $media->slug = $media->generateSlug(6);

            if ($media->wasChanged('file') && strpos($media->file, config('app.cdn.videos')) !== false) {
                $media->file = str_replace(config('app.cdn.videos'), '', $media->file);
            }

            if ($media->status === self::STATUS_DENIED) {
                $media->visibility = self::VISIBILITY_PRIVATE;
            }
        });

        static::created(function ($media) {
            $creatorUser = $media->userObj;

            if ($media->wasChanged('status')) {
                $creatorUserMedias = $creatorUser->medias;
                foreach ($creatorUserMedias as $creatorUserMedia) {
                    if ((string)$creatorUserMedia['_id'] === (string)$media->_id) {
                        $creatorUser->removeFromSet('medias', $creatorUserMedia);
                    }
                }

                $creatorUser->media_count = self::creator($media->created_by)->count();
                $medias = [];
                $completedMedias = self::creator($media->created_by)->completed()->recentFirst()->limit(30)->asArray()->all();
                foreach ($completedMedias as $completedMedia) {
                    $basename = basename($completedMedia['file'], '.' . pathinfo($completedMedia['file'], PATHINFO_EXTENSION));
                    $file = config('app.cdn.videos') . $completedMedia['file'];
                    $cover = config('app.cdn.covers') . $basename . '.jpg';
                    $medias[] = ['_id' => new ObjectId($completedMedia['_id']), 'file' => $file, 'cover' => $cover, 'status' => $completedMedia['status']];
                }
                $creatorUser->medias = $medias;
                $creatorUser->count_fields_updated_at = array_merge(
                    $creatorUser->count_fields_updated_at,
                    ['medias' => DT::utcNow()]
                );
                $creatorUser->save();
            }

            if ($media->status === self::STATUS_COMPLETED || $media->status === self::STATUS_CONFIRMED) {
                Cache::forget('Media.Index.TotalCount.Public');
            }

            if ($media->status === self::STATUS_USER_DELETED) {
                Cache::forget('Media.Index.TotalCount.Public');
                $creatorUser->refresh();

                $creatorUser->media_count--;

                $file = config('app.cdn.videos') . $media->file;
                $cover = config('app.cdn.covers') . $media->filename . '.jpg';
                $creatorUser->removeFromSet('medias', ['_id' => $media->_id, 'file' => $file, 'cover' => $cover, 'status' => $media->status]);
                $creatorUser->count_fields_updated_at = array_merge(
                    $creatorUser->count_fields_updated_at,
                    ['medias' => DT::utcNow()]
                );
                $creatorUser->save();

                dispatch((new DeleteMediaLike($media->id, $creatorUser->_id))->onQueue('low'));
            }

            dispatch((new UploadMedia($media->file, (string)$media->_id))->onQueue('high'));

            if ($media->wasChanged('status') && $media->status === self::STATUS_CONFIRMED) {
                /*
                Handler::Push($media->created_by, 'media.confirmed', [
                    'media' => $media->simple_array,
                    'user' => $media->creator,
                    'message' => $media->creator['username'] . 'likes your video.',
                ]);
                */
            }
        });
    }

    public static function getVisibilities()
    {
        return [
            self::VISIBILITY_PRIVATE => __('Private'),
            self::VISIBILITY_PUBLIC => __('Public'),
        ];
    }

    public static function getStatuses()
    {
        return [
            self::STATUS_QUEUED => __('Queued'),
            self::STATUS_UPLOADED => __('Uploaded'),
            self::STATUS_IN_PROGRESS => __('In-Progress'),
            self::STATUS_COMPLETED => __('Waiting For Review'),
            self::STATUS_FAILED => __('Failed'),
            self::STATUS_CONFIRMED => __('Confirmed'),
            self::STATUS_DENIED => __('Denied'),
            self::STATUS_ADMIN_DELETED => __('Deleted By Admin'),
            self::STATUS_USER_DELETED => __('Deleted'),
            self::STATUS_IN_REVIEW => __('Under review'),
        ];
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return MediaFactory::new();
    }

    public function getCountFieldsUpdatedAtAttribute($attributeValue)
    {
        foreach ($attributeValue as $field => $value) {
            /* MongoDB\BSON\UTCDateTime $value */
            $attributeValue[$field] = ($value instanceof UTCDateTime) ? $value->toDateTime()->getTimestamp() : $value;
        }

        return $attributeValue;
    }

    public function setCountFieldsUpdatedAtAttribute($attributeValue)
    {
        foreach ($attributeValue as $field => $value) {
            /* MongoDB\BSON\UTCDateTime $value */
            $attributeValue[$field] = ($value instanceof UTCDateTime) ? $value : DT::timestampToUtc($value);
        }

        $this->attributes['count_fields_updated_at'] = $attributeValue;
    }

    /**
     * Get the phone associated with the user.
     */
    public function userObj()
    {
        return $this->belongsTo(User::class, 'user_id');
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
                    return $score['score'];
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
    public function getAlertsAttribute()
    {
        if (auth()->guest()) {
            return [];
        }

        return Alert::media($this->_id)->user(auth()->user()->_id)->notVisited()->get();
    }

    /**
     * Get the media's skin score.
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
     * Get the user's full name.
     */
    public function getIsCompletedAttribute(): bool
    {
        return in_array(
            $this->status,
            [self::STATUS_COMPLETED, self::STATUS_CONFIRMED, self::STATUS_ADMIN_DELETED],
            true
        );
    }

    /**
     * @throws Exception
     */
    public function setSlugAttribute(): string
    {
        return $this->attributes['slug'] = $this->generateSlug(6);
    }

    /**
     * @throws Exception
     */
    public function generateSlug(int $length): string
    {
        $slug = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, $length);

        return (null === self::slug($slug)->first()) ? $slug : $this->generateSlug($length);
    }

    /**
     * store media tags
     */
    public function parseDescription()
    {
        $description = trim($this->description);
        $tags = $people = [];
        foreach (explode(' ', $description) as $item) {
            if (isset($item[0]) && $item[0] === '#' && substr_count($item, '#') === 1) {
                $tags[] = substr($item, 1);
            }
            if (isset($item[0]) && $item[0] === '@' && substr_count($item, '@') === 1) {
                $people[] = substr($item, 1);
            }
        }
        $this->hashtags = array_slice($tags, 0, 20);
        $people = array_slice($people, 0, 20);
        $users = [];
        $usersQuery = User::select(['username', 'avatar', '_id'])->usernames($people)->limit(20)->get();
        foreach ($usersQuery->toArray() as $user) {
            $users[] = $this->createSimpleUser($user);
        }
        $this->people = $users;
    }

    /**
     * store media tags
     * @param ObjectId|null $userId
     * @return mixed
     */
    public function isVisibleBy(ObjectId $userId = null)
    {
        if ($this->visibility === self::VISIBILITY_PUBLIC) {
            return true;
        }

        if ($this->visibility === self::VISIBILITY_PRIVATE && $userId === null) {
            return false;
        }

        $isFollowed = Follow::select(['created_by', '_id'])
            ->creator($userId)
            ->user($this->created_by)
            ->accepted()
            ->exists();
        if (!empty($isFollowed)) {
            return true;
        }

        return false;
    }
}
