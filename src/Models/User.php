<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Database\Factories\UserFactory;
use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Models\Scopes\UserScope;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Redis;
use Jenssegers\Mongodb\Auth\User as Authenticatable;
use Maklad\Permission\Traits\HasRoles;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * User model.
 *
 * @property ObjectId    $_id
 * @property string      $username
 * @property string      $password_hash
 * @property string      $password_reset_token
 * @property string      $email
 * @property bool        $email_verified
 * @property string      $phone_number
 * @property bool        $phone_number_verified
 * @property string      $auth_key
 * @property string      $avatar
 * @property int         $status
 * @property int         $gender
 * @property int         $visibility
 * @property int         $show_online_status
 * @property int         $interested_in
 * @property int         $block_count
 * @property int         $follower_count
 * @property int         $following_count
 * @property int         $like_count
 * @property int         $followed_hashtag_count
 * @property int         $media_count
 * @property UTCDateTime $created_at
 * @property UTCDateTime $updated_at
 * @property array       $setting
 * @property array       $features
 * @property mixed       $authLogs
 * @property mixed       $id
 * @property string      $password_hash_field
 * @property string      $authKey
 * @property array       $links
 * @property bool        $require_otp
 * @property bool        $is_protected
 * @property array       $default_setting
 * @property array       $count_fields_updated_at
 * @property array       $subscriptions
 * @property array       $subscription_plan
 *
 * @property-read string $admin_url
 * @property-read string $slack_admin_url
 * @property-read bool $is_subscribable
 * @property-read bool $is_online
 * @property-read bool $is_online_for_followers
 */
class User extends Authenticatable implements JWTSubject
{
    use HasFactory;
    use Notifiable;
    use UserScope;
    use HasRoles;

    public const TYPE_USER = 0;
    public const TYPE_ADMIN = 1;

    public const STATUS_PENDING = 0;
    public const STATUS_VERIFIED = 1;
    public const STATUS_ACTIVE = 2;
    public const STATUS_SUSPENDED = 3;
    public const STATUS_BLOCKED = 4;
    public const STATUS_DEACTIVATED = 10;

    public const GENDER_FEMALE = 0;
    public const GENDER_MALE = 1;
    public const GENDER_TRANSGENDER = 2;
    public const GENDER_NOT_MENTION = 3;

    public const INTERESTED_IN_FEMALE = 0;
    public const INTERESTED_IN_MALE = 1;
    public const INTERESTED_IN_TRANSGENDER = 2;
    public const INTERESTED_IN_COUPLE = 3;

    public const VISIBILITY_PUBLIC = 1;
    public const VISIBILITY_PRIVATE = 0;

    public const SHOW_ONLINE_STATUS_NONE = 0;
    public const SHOW_ONLINE_STATUS_FOLLOWERS = 1;
    public const SHOW_ONLINE_STATUS_ALL = 2;

    public const FEATURE_TIPS = 'tips';
    public const FEATURE_DEMO = 'demo';

    /**
     * The collection associated with the model.
     *
     * @var string
     */
    protected $collection = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        '_id',
        'username',
        'email',
        'email_verified',
        'phone_number',
        'phone_number_verified',
        'bio',
        'full_name',
        'avatar',
        'auth_key',
        'password_hash',
        'password_reset_token',
        'setting',
        'features',
        'gender',
        'interested_in',
        'type',
        'status',
        'visibility',
        'show_online_status',
        'follower_count',
        'following_count',
        'block_count',
        'followed_hashtag_count',
        'like_count',
        'media_count',
        'count_fields_updated_at',
        'blocks',
        'likes',
        'followers',
        'followings',
        'followed_hashtags',
        'medias',
        'promo_link',
        'referral_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $attributes = [
        'count_fields_updated_at' => [],
        'setting' => [
            'otp' => false,
            'notifications' => [
                'unread_message_alerts' => false,
                'new_followers' => false,
                'news_and_updates' => false,
                'tips' => false,
                'new_subscribers' => false,
            ],
        ],
        'features' => [
            'tips' => false,
            'demo' => false,
        ],
        'status' => self::STATUS_PENDING,
        'email_verified' => false,
        'phone_number_verified' => false,
        'gender' => self::GENDER_MALE,
        'interested_in' => self::INTERESTED_IN_MALE,
        'visibility' => self::VISIBILITY_PUBLIC,
        'follower_count' => 0,
        'show_online_status' => self::SHOW_ONLINE_STATUS_ALL,
        'following_count' => 0,
        'like_count' => 0,
        'block_count' => 0,
        'followed_hashtag_count' => 0,
        'media_count' => 0,
        'subscriptions' => [],
        'subscription_plan' => [],
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'username' => 'string',
        'full_name' => 'string',
        'email' => 'string',
        'status' => 'integer',
        'email_verified' => 'boolean',
        'phone_number_verified' => 'boolean',
        'gender' => 'integer',
        'avatar' => 'string',
        'interested_in' => 'integer',
        'visibility' => 'integer',
        'follower_count' => 'integer',
        'following_count' => 'integer',
        'like_count' => 'integer',
        'block_count' => 'integer',
        'followed_hashtag_count' => 'integer',
        'media_count' => 'integer',
        'type' => 'integer',
    ];

    protected $dates = [
        'email_verified_at',
        'phone_number_verified_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public static function getFeatures(): array
    {
        return [
            self::FEATURE_TIPS => __('tips'),
            self::FEATURE_DEMO => __('demo'),
        ];
    }

    public static function getGenders(): array
    {
        return [
            self::GENDER_FEMALE => __('female'),
            self::GENDER_MALE => __('male'),
            self::GENDER_TRANSGENDER => __('transgender'),
            self::GENDER_NOT_MENTION => __('not mention'),
        ];
    }

    public static function getInterestedIns(): array
    {
        return [
            self::INTERESTED_IN_FEMALE => __('female'),
            self::INTERESTED_IN_MALE => __('male'),
            self::INTERESTED_IN_TRANSGENDER => __('transgender'),
            self::INTERESTED_IN_COUPLE => __('couple'),
        ];
    }

    public static function getTypes(): array
    {
        return [
            self::TYPE_USER => __('user'),
            self::TYPE_ADMIN => __('admin'),
        ];
    }

    public static function getVisibilities(): array
    {
        return [
            self::VISIBILITY_PRIVATE => __('private'),
            self::VISIBILITY_PUBLIC => __('public'),
        ];
    }

    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => __('pending'),
            self::STATUS_VERIFIED => __('verified'),
            self::STATUS_ACTIVE => __('active'),
            self::STATUS_SUSPENDED => __('suspended'),
            self::STATUS_BLOCKED => __('banned'),
            self::STATUS_DEACTIVATED => __('deleted'),
        ];
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return UserFactory::new();
    }

    public function getSlackAdminUrlAttribute()
    {
        return "<{$this->admin_url}|@{$this->username}>";
    }

    public function getAdminUrlAttribute()
    {
        return route('core.admin.user.view', ['user' => $this->_id]);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'device_id' => request()?->header('x-device-id'),
        ];
    }

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    /**
     * Route notifications for the Slack channel.
     *
     * @param Notification $notification
     *
     * @return string
     */
    public function routeNotificationForSlack($notification)
    {
        return config('app.slack_webhook_url');
    }

    public function addToSet(string $attribute, mixed $item, int $length = null): void
    {
        if (! is_array($this->$attribute)) {
            $this->$attribute = [];
        }
        $values = $this->$attribute;
        if (! in_array($item, $values, false)) {
            array_unshift($values, $item);
        }

        if (null !== $length) {
            $values = array_slice($values, 0, $length);
        }

        $this->$attribute = $values;
    }

    public function removeFromSet(string $attribute, mixed $item): void
    {
        if (! is_array($this->$attribute)) {
            $this->$attribute = [];
        }
        $values = $this->$attribute;
        if (($key = array_search($item, $values, false)) !== false) {
            unset($values[$key]);
            if (is_int($key)) {
                $values = array_values($values);
            }
        }

        $this->$attribute = $values;
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
    public function mediaObjs()
    {
        return $this->hasMany(Media::class, 'created_by');
    }

    /**
     * Get the media's skin score.
     *
     * @return array
     */
    public function getAlertsAttribute()
    {
        if (auth()->guest() || ((string) $this->_id !== (string) auth()->user()->_id)) {
            return [];
        }

        return Alert::user(auth()->user()->_id)->notVisited()->get();
    }

    /**
     * Get if the current login user follow this user or not.
     */
    public function getIsFollowedAttribute(): bool
    {
        if (auth()->guest()) {
            return false;
        }

        $cacheKey = (new Follow())->getCollection().':creator:'.auth()->user()->_id;
        Follow::cacheByUserId(auth()->user()->_id);

        return Redis::sismember($cacheKey, (string) $this->_id);
    }

    /**
     * @return string
     */
    public function getCollection(): string
    {
        return $this->collection;
    }

    public function setAttribute($key, $value)
    {
        if ($this->hasCast($key)) {
            $value = $this->castAttribute($key, $value);
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * @return bool
     */
    public function getIsSubscribableAttribute(): bool
    {
        return isset($this->subscription_plan['amount'], $this->subscription_plan['currency'], $this->subscription_plan['days']);
    }

    public function getIsOnlineAttribute(): bool
    {
        $currentMinute = date('i');
        $currentMinuteWindow = $currentMinute - ($currentMinute % 5);
        $currentWindow = date('H').$currentMinuteWindow;

        $cacheKey = config('app.cache.keys.online.all').':'.$currentWindow;
        return Redis::sismember($cacheKey, (string) $this->_id);
    }

    public function getIsOnlineForFollowersAttribute(): bool
    {
        $currentMinute = date('i');
        $currentMinuteWindow = $currentMinute - ($currentMinute % 5);
        $currentWindow = date('H').$currentMinuteWindow;

        $cacheKey = config('app.cache.keys.online.followings').':'.$currentWindow;
        return Redis::sismember($cacheKey, (string) $this->_id);
    }
}
