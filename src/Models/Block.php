<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Database\Factories\BlockFactory;
use Aparlay\Core\Models\Scopes\BlockScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use MongoDB\BSON\ObjectId;

/**
 * Class Block
 * @package Aparlay\Core\Models
 *
 * @property ObjectId $_id
 * @property string $hashtag
 * @property array $user
 * @property array $creator
 * @property bool $is_deleted
 * @property string $created_at
 *
 * @property-read User $creatorObj
 * @property-read User $userObj
 * @property-read null|mixed $creator_id
 * @property-read null|mixed $user_id
 * @property string $aliasModel
 *
 * @method static|self|Builder deleted() get deleted blocks
 * @method static|self|Builder notDeleted() get not deleted blocks
 * @method static|self|Builder creator(ObjectId|string $userId) get creator user
 * @method static|self|Builder user(ObjectId|string $userId) get blocked user
 */
class Block extends Model
{
    use HasFactory;
    use Notifiable;
    use BlockScope;

    /**
     * The collection associated with the model.
     * @var string
     */
    protected $collection = 'user_blocks';

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
        '_id' => 'string',
        'created_at' => 'datetime',
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return Factory
     */
    protected static function newFactory(): Factory
    {
        return BlockFactory::new();
    }
}
