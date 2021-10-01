<?php

namespace Aparlay\Core\Api\V1\Models;

use Aparlay\Core\Api\V1\Casts\SimpleUser;
use Aparlay\Core\Models\Follow as FollowBase;
use Aparlay\Core\Models\User;
use Illuminate\Database\Eloquent\Builder;
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
class Follow extends FollowBase
{
    use UserFieldTrait;
    use CreatorFieldTrait;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'user' => SimpleUser::class,
        'creator' => SimpleUser::class,
    ];
}
