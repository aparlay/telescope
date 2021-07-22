<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Database\Factories\UserFactory;
use Aparlay\Core\Models\Scopes\UserScope;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Jenssegers\Mongodb\Auth\User as Authenticatable;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

/**
 * User model
 *
 * @property ObjectId $_id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property bool $email_verified
 * @property string $phone_number
 * @property bool $phone_number_verified
 * @property string $auth_key
 * @property string $avatar
 * @property int $status
 * @property int $visibility
 * @property int $block_count
 * @property int $follower_count
 * @property int $following_count
 * @property int $like_count
 * @property int $followed_hashtag_count
 * @property int $media_count
 * @property UTCDateTime $created_at
 * @property UTCDateTime $updated_at
 * @property array $setting
 * @property array $features
 * @property-read mixed $authLogs
 * @property-read mixed $id
 * @property-write string $passwordHashField
 * @property-read string $authKey
 * @property-read array $links
 * @property-read bool $require_otp
 * @property-read bool $is_protected
 * @property array $defaultSetting
 *
 * @OA\Schema()
 *
 */
class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use UserScope;

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

    public const FEATURE_TIPS = 'tips';
    public const FEATURE_DEMO = 'demo';

    /**
     * The collection associated with the model.
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
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        '_id' => 'string',
        'email_verified_at' => 'datetime',
        'phone_number_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return Factory
     */
    protected static function newFactory(): Factory
    {
        return UserFactory::new();
    }

    /**
     * @return array
     */
    public static function getFeatures(): array
    {
        return [
            self::FEATURE_TIPS => __('Tips'),
            self::FEATURE_DEMO => __('Demo'),
        ];
    }

    /**
     * @return array
     */
    public static function getGenders(): array
    {
        return [
            self::GENDER_FEMALE => __('Female'),
            self::GENDER_MALE => __('Male'),
            self::GENDER_TRANSGENDER => __('Transgender'),
            self::GENDER_NOT_MENTION => __('Not Mention'),
        ];
    }

    /**
     * @return array
     */
    public static function getInterestedIns(): array
    {
        return [
            self::INTERESTED_IN_FEMALE => __('Female'),
            self::INTERESTED_IN_MALE => __('Male'),
            self::INTERESTED_IN_TRANSGENDER => __('Transgender'),
            self::INTERESTED_IN_COUPLE => __('Couple'),
        ];
    }

    /**
     * @return array
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_USER => __('User'),
            self::TYPE_ADMIN => __('Admin'),
        ];
    }

    /**
     * @return array
     */
    public static function getVisibilities(): array
    {
        return [
            self::VISIBILITY_PRIVATE => __('Private'),
            self::VISIBILITY_PUBLIC => __('Public'),
        ];
    }

    /**
     * @return array
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => __('Pending'),
            self::STATUS_VERIFIED => __('Verified'),
            self::STATUS_ACTIVE => __('Active'),
            self::STATUS_SUSPENDED => __('Suspended'),
            self::STATUS_BLOCKED => __('Banned'),
            self::STATUS_DEACTIVATED => __('Deleted'),
        ];
    }
}
