<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Api\V1\Models\Block;
use Aparlay\Core\Api\V1\Services\OnlineUserService;
use Aparlay\Core\Database\Factories\UserFactory;
use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Models\Enums\UserFeature;
use Aparlay\Core\Models\Enums\UserGender;
use Aparlay\Core\Models\Enums\UserInterestedIn;
use Aparlay\Core\Models\Enums\UserStatus;
use Aparlay\Core\Models\Enums\UserType;
use Aparlay\Core\Models\Enums\UserVerificationStatus;
use Aparlay\Core\Models\Enums\UserVisibility;
use Aparlay\Core\Models\Scopes\UserScope;
use Aparlay\Core\Models\Traits\CountryFields;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Jenssegers\Mongodb\Auth\User as Authenticatable;
use JetBrains\PhpStorm\ArrayShape;
use Laravel\Scout\Searchable;
use Maklad\Permission\Traits\HasRoles;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

/**
 * User model.
 *
 * @property ObjectId    $_id
 * @property string      $username
 * @property string      $full_name
 * @property string      $password_hash
 * @property string      $password_reset_token
 * @property string      $email
 * @property bool        $email_verified
 * @property string      $phone_number
 * @property bool        $phone_number_verified
 * @property string      $auth_key
 * @property string      $bio
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
 * @property array       $user_agents
 * @property array       $subscribed_to
 * @property array       $stats
 * @property array       $last_location
 * @property string      $country_alpha2
 * @property string      $country_label
 * @property string      $country_flag
 * @property array       $country_flags
 * @property array       $text_search
 * @property int         $verification_status
 * @property array       $scores
 * @property string      $deactivation_reason
 * @property bool        $has_unread_chat
 * @property bool        $has_notification
 * @property UTCDateTime $last_online_at
 *
 * @property-read string $admin_url
 * @property-read string $note_admin_url
 * @property-read string $slack_admin_url
 * @property-read bool $is_subscribable
 * @property-read bool $is_online
 * @property-read bool $is_verified
 * @property-read bool $is_online_for_followers
 * @property-read bool $is_tier3
 * @property-read bool $is_tier1
 * @property-read bool $is_risky
 *
 * @method static |self|Builder username(string $username) get user
 * @method static |self|Builder user(ObjectId|string $userId)    get user
 * @method static |self|Builder availableForFollower()    get available content for followers
 */
class User extends Authenticatable implements JWTSubject
{
    use HasFactory;
    use Notifiable;
    use UserScope;
    use HasRoles;
    use Searchable;
    use CountryFields;

    public const FEATURE_TIPS = 'tips';
    public const FEATURE_DEMO = 'demo';

    public const ROLE_SUPER_ADMINISTRATOR = 'super-administrator';
    public const ROLE_ADMINISTRATOR = 'administrator';
    public const ROLE_SUPPORT = 'support';

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
        'country_alpha2',
        'payout_country_alpha2',
        'user_agents',
        'stats',
        'last_location',
        'text_search',
        'scores',
        'verification_status',
        'deactivation_reason',
        'created_at',
        'updated_at',
        'deleted_at',
        'last_online_at',
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
            'payment' => [
                'allow_unverified_cc' => false,
                'block_unverified_cc' => false,
                'block_cc_payments' => false,
                'spent_amount' => 0,
            ],
        ],
        'features' => [
            'tips' => false,
            'exclusive_content' => false,
            'wallet_bank' => false,
            'wallet_paypal' => false,
            'wallet_cryptocurrency' => false,
            'demo' => false,
        ],
        'email_verified' => false,
        'phone_number_verified' => false,
        'follower_count' => 0,
        'following_count' => 0,
        'like_count' => 0,
        'block_count' => 0,
        'followed_hashtag_count' => 0,
        'media_count' => 0,
        'scores' => [
            'sort' => 0,
            'risk' => 0,
        ],
        'subscriptions' => [],
        'subscription_plan' => [],
        'user_agents' => [],
        'search' => [],
        'subscribed_to' => [],
        'stats' => [
            'amounts' => [
                'sent_tips' => 0,
                'received_tips' => 0,
                'subscriptions' => 0,
                'subscribers' => 0,
            ],
            'counters' => [
                'followers' => 0,
                'followings' => 0,
                'likes' => 0,
                'blocks' => 0,
                'followed_hashtags' => 0,
                'medias' => 0,
                'subscriptions' => 0,
                'subscribers' => 0,
            ],
        ],
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
        'deactivation_reason' => 'string',
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
        'verification_status' => 'integer',
    ];

    protected $dates = [
        'email_verified_at',
        'phone_number_verified_at',
        'last_online_at',
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
     *
     * @return bool
     */
    public function shouldBeSearchable(): bool
    {
        return $this->visibility == UserVisibility::PUBLIC->value && ! config('app.is_testing');
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
            'type' => 'user',
            'poster' => $this->avatar,
            'username' => $this->username,
            'full_name' => $this->full_name,
            'description' => $this->bio,
            'hashtags' => [],
            'score' => $this->scores['sort'],
            'country' => $this->country_alpha2,
            'like_count' => $this->like_count,
            'last_online_at' => $this->last_online_at ? $this->last_online_at->valueOf() : 0,
            'visit_count' => 0,
            'comment_count' => 0,
            '_geo' => $this->last_location ?? ['lat' => 0.0, 'lng' => 0.0],
        ];
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return UserFactory::new();
    }

    /**
     * Get the phone associated with the user.
     */
    public function mediaObjs(): HasMany|\Jenssegers\Mongodb\Relations\HasMany
    {
        return $this->hasMany(Media::class, 'created_by');
    }

    /**
     * Get the phone associated with the user.
     */
    public function userDocumentObjs(): HasMany|\Jenssegers\Mongodb\Relations\HasMany
    {
        return $this->hasMany(UserDocument::class, 'creator._id');
    }

    /**
     * Get all the user's notifications.
     */
    public function userNotificationObjs()
    {
        return $this->morphMany(UserNotification::class, 'entity.');
    }

    /**
     * @return string
     */
    public function getSlackAdminUrlAttribute(): string
    {
        return "<{$this->admin_url}|@{$this->username}>";
    }

    /**
     * @return string
     */
    public function getNoteAdminUrlAttribute(): string
    {
        return "<a href='{$this->admin_url}' title='{$this->username}'>{$this->username}</a>";
    }

    /**
     * @return string
     */
    public function getAdminUrlAttribute(): string
    {
        return route('core.admin.user.view', ['user' => $this->_id]);
    }

    /**
     * Get the media's skin score.
     *
     * @return array|Collection
     */
    public function getAlertsAttribute(): array|Collection
    {
        return Alert::user($this->_id)->userOnly()->notVisited()->get();
    }

    /**
     * Get the user risk.
     * @todo this method implementation should change and rely on risk score
     *
     * @return bool
     */
    public function getIsRiskyAttribute(): bool
    {
        return $this->setting['payment']['block_unverified_cc'] ||
            ($this->is_tier3) ||
            ($this->setting['payment']['spent_amount'] > config('payment.fraud.big_spender.maximum_total_amount'));
    }

    /**
     * Get the user country tier.
     *
     * @return bool
     */
    public function getIsTier3Attribute(): bool
    {
        return in_array(Str::upper($this->country_alpha2), config('core.tiers.3') ?? [] , true);
    }

    /**
     * Get the user country tier.
     *
     * @return bool
     */
    public function getIsTier1Attribute(): bool
    {
        return in_array(Str::upper($this->country_alpha2), config('core.tiers.1') ?? [], true);
    }

    /**
     * Get if the current login user follow this user or not.
     */
    public function getIsFollowedAttribute(): bool
    {
        if (auth()->guest()) {
            return false;
        }

        $userId = auth()->user()->_id;
        Follow::cacheByUserId(auth()->user()->_id);

        return Follow::checkCreatorIsFollowedByUser((string) $this->_id, (string) $userId);
    }

    /**
     * @return bool
     */
    public function getIsSubscribableAttribute(): bool
    {
        return isset($this->subscription_plan['amount'], $this->subscription_plan['currency'], $this->subscription_plan['days']);
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
     * @return mixed
     */
    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    /**
     * @return null[]
     */
    #[ArrayShape(['device_id' => 'array|null|string'])]
    public function getJWTCustomClaims(): array
    {
        return [
            'device_id' => request()?->header('x-device-id'),
        ];
    }

    /**
     * @return string
     */
    public function getAuthPassword(): string
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
    public function routeNotificationForSlack($notification): string
    {
        return config('app.slack_webhook_url');
    }

    /**
     * @param  string  $attribute
     * @param  mixed  $item
     * @param  int|null  $length
     * @return void
     */
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

    /**
     * @param  string  $attribute
     * @param  mixed  $item
     * @return void
     */
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

    /**
     * @return array
     */
    public static function getFeatures(): array
    {
        return [
            UserFeature::TIPS->value => UserFeature::TIPS->label(),
            UserFeature::DEMO->value => UserFeature::DEMO->label(),
        ];
    }

    /**
     * @return array
     */
    public static function getGenders(): array
    {
        return [
            UserGender::FEMALE->value => UserGender::FEMALE->label(),
            UserGender::MALE->value => UserGender::MALE->label(),
            UserGender::TRANSGENDER->value => UserGender::TRANSGENDER->label(),
            UserGender::NOT_MENTION->value => UserGender::NOT_MENTION->label(),
        ];
    }

    /**
     * @return array
     */
    public static function getInterestedIns(): array
    {
        return [
            UserInterestedIn::FEMALE->value => UserInterestedIn::FEMALE->label(),
            UserInterestedIn::MALE->value => UserInterestedIn::MALE->label(),
            UserInterestedIn::TRANSGENDER->value => UserInterestedIn::TRANSGENDER->label(),
            UserInterestedIn::COUPLE->value => UserInterestedIn::COUPLE->label(),
        ];
    }

    /**
     * @return array
     */
    public static function getTypes(): array
    {
        return [
            UserType::USER->value => UserType::USER->label(),
            UserType::ADMIN->value => UserType::ADMIN->label(),
        ];
    }

    /**
     * @return array
     */
    public static function getVisibilities(): array
    {
        return [
            UserVisibility::PRIVATE->value => UserVisibility::PRIVATE->label(),
            UserVisibility::PUBLIC->value => UserVisibility::PUBLIC->label(),
        ];
    }

    /**
     * @return array
     */
    public static function getStatuses(): array
    {
        return [
            UserStatus::PENDING->value => UserStatus::PENDING->label(),
            UserStatus::VERIFIED->value => UserStatus::VERIFIED->label(),
            UserStatus::ACTIVE->value => UserStatus::ACTIVE->label(),
            UserStatus::SUSPENDED->value => UserStatus::SUSPENDED->label(),
            UserStatus::BLOCKED->value => UserStatus::BLOCKED->label(),
            UserStatus::DEACTIVATED->value => UserStatus::DEACTIVATED->label(),
        ];
    }

    /**
     * @return array
     */
    public static function getVerificationStatuses(): array
    {
        return [
            UserVerificationStatus::UNDER_REVIEW->value => UserVerificationStatus::UNDER_REVIEW->label(),
            UserVerificationStatus::VERIFIED->value => UserVerificationStatus::VERIFIED->label(),
            UserVerificationStatus::PENDING->value => UserVerificationStatus::PENDING->label(),
            UserVerificationStatus::REJECTED->value => UserVerificationStatus::REJECTED->label(),
            UserVerificationStatus::UNVERIFIED->value => UserVerificationStatus::UNVERIFIED->label(),
        ];
    }

    public static function isOnline($userId): bool
    {
        [$currentWindow, $nextWindow] = OnlineUserService::timeWindows();

        $cacheKey = config('app.cache.keys.online.none').':'.$currentWindow;

        return Redis::sismember($cacheKey, (string) $userId);
    }

    public static function isOnlineForFollowers($userId): bool
    {
        [$currentWindow, $nextWindow] = OnlineUserService::timeWindows();

        $cacheKey = config('app.cache.keys.online.followings').':'.$currentWindow;

        return Redis::sismember($cacheKey, (string) $userId);
    }

    public static function isOnlineForAll($userId): bool
    {
        [$currentWindow, $nextWindow] = OnlineUserService::timeWindows();

        $cacheKey = config('app.cache.keys.online.all').':'.$currentWindow;

        return Redis::sismember($cacheKey, (string) $userId);
    }

    public function getIsVerifiedAttribute()
    {
        return $this->verification_status === UserVerificationStatus::VERIFIED->value;
    }

    public function getCountryAlpha3Attribute()
    {
        return $this->country_alpha2 ? \Aparlay\Core\Helpers\Country::getAlpha3ByAlpha2($this->country_alpha2) : '';
    }

    public function getVerificationStatusLabelAttribute(): string
    {
        return UserVerificationStatus::from($this->verification_status)->label();
    }

    public function getStatusLabelAttribute()
    {
        return UserStatus::from($this->status)->label();
    }

    public function getIsOnlineAttribute(): bool
    {
        return self::isOnline($this->_id);
    }

    public function getIsOnlineForFollowersAttribute(): bool
    {
        return self::isOnlineForFollowers($this->_id);
    }

    public function getIsOnlineForAllAttribute(): bool
    {
        return self::isOnlineForAll($this->_id);
    }

    /**
     * @param  User|Authenticatable|ObjectId|string|null  $user
     * @return bool
     */
    public function equalTo(self|Authenticatable|ObjectId|string|null $user): bool
    {
        if ($user instanceof ObjectId) {
            $userId = (string) $user;
        } elseif ($user instanceof Authenticatable) {
            $userId = (string) $user->_id;
        } else {
            $userId = (string) $user;
        }

        return (string) $this->_id === $userId;
    }

    /**
     * Get only class name without namespace.
     * @return bool|string
     */
    public static function shortClassName()
    {
        return substr(strrchr(static::class, '\\'), 1);
    }

    /**
     * Get only class name without namespace.
     * @param  User|Authenticatable|ObjectId|string  $user
     * @return bool
     */
    public function blockedUser(self | Authenticatable | ObjectId | string $user): bool
    {
        if (is_string($user)) {
            $user = new ObjectId($user);
        }

        if ($user instanceof ObjectId) {
            $user = self::findOrFail($user);
        }

        return Block::creator($user->_id)->user($this->_id)->exists() ||
            Block::user($user->_id)->creator($this->_id)->exists() ||
            $this->blockedCountry($user->country_alpha2);
    }

    /**
     * Get only class name without namespace.
     * @param  string  $countryAlpha2
     * @return bool
     */
    public function blockedCountry(string $countryAlpha2): bool
    {
        return Block::creator($this->_id)->country($countryAlpha2)->exists();
    }

    /**
     * @param  int  $amount
     * @return bool
     */
    public function unverifiedCCSpentAmount(int $amount): bool
    {
        $setting = $this->setting;
        $setting['payment']['spent_amount'] += $amount;

        return $this->update(['setting' => $setting]);
    }
}
