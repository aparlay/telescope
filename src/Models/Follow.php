<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Api\V1\Models\CreatorFieldTrait;
use Aparlay\Core\Api\V1\Models\UserFieldTrait;
use Aparlay\Core\Database\Factories\FollowFactory;
use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Models\Scopes\FollowScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use MongoDB\BSON\ObjectId;

/**
 * Class Follow.
 *
 * @property ObjectId   $_id
 * @property string     $hashtag
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
 *
 * @method static |self|Builder creator(ObjectId|string $userId) get creator user
 * @method static |self|Builder user(ObjectId|string $userId)    get blocked user
 */
class Follow extends BaseModel
{
    use HasFactory;
    use Notifiable;
    use FollowScope;
    use UserFieldTrait;
    use CreatorFieldTrait;

    public const STATUS_PENDING = 0;

    public const STATUS_ACCEPTED = 1;

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
    ];

    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => __('Pending'),
            self::STATUS_ACCEPTED => __('Accepted'),
        ];
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return FollowFactory::new();
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
}
