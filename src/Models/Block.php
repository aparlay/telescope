<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Casts\SimpleUserCast;
use Aparlay\Core\Database\Factories\BlockFactory;
use Aparlay\Core\Models\Queries\BlockQueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use MongoDB\BSON\ObjectId;

/**
 * Class Block.
 *
 * @property ObjectId   $_id
 * @property string     $aliasModel
 * @property string     $created_at
 * @property array      $creator
 * @property mixed|null $creator_id
 * @property User       $creatorObj
 * @property string     $hashtag
 * @property bool       $is_deleted
 * @property array      $user
 * @property mixed|null $user_id
 * @property User       $userObj
 */
class Block extends BaseModel
{
    use HasFactory;
    use Notifiable;

    /**
     * The collection associated with the model.
     *
     * @var string
     */
    protected $collection = 'user_blocks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable   = [
        '_id',
        'user',
        'creator',
        'country_alpha2',
        'is_deleted',
        'created_at',
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
        'creator' => SimpleUserCast::class . ':_id,username,avatar,is_liked,is_followed,is_verified',
        'user' => SimpleUserCast::class . ':_id,username,avatar,is_liked,is_followed,is_verified',
    ];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return BlockFactory::new();
    }

    public static function query(): BlockQueryBuilder|Builder
    {
        return parent::query();
    }

    public function newEloquentBuilder($query): BlockQueryBuilder
    {
        return new BlockQueryBuilder($query);
    }

    /**
     * Get the user associated with the follow.
     */
    public function userObj(): BelongsTo|\Jenssegers\Mongodb\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user._id');
    }

    /**
     * Get the creator associated with the follow.
     */
    public function creatorObj(): BelongsTo|\Jenssegers\Mongodb\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'creator._id');
    }
}
