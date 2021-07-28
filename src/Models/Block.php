<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Database\Factories\BlockFactory;
use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Models\Scopes\BlockScope;
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
        'userObj', 'creatorObj'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        '_id' => 'string',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
        'deleted_at' => 'timestamp',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::created(function ($block) {
            $block->creatorObj->block_count++;
            $block->creatorObj->addToSet('blocks', [
                '_id' => new ObjectId($block->user['_id']),
                'username' => $block->user['username'],
                'avatar' => $block->user['avatar']
            ]);
            $block->creatorObj->count_fields_updated_at = array_merge(
                $block->creatorObj->count_fields_updated_at,
                ['blocks' => DT::utcNow()]
            );
            $block->creatorObj->save();
        });

        static::deleted(function ($block) {
            $block->creatorObj->block_count--;
            $block->creatorObj->removeFromSet('blocks', [
                '_id' => new ObjectId($block->user['_id']),
                'username' => $block->user['username'],
                'avatar' => $block->user['avatar']
            ]);
            $block->creatorObj->count_fields_updated_at = array_merge(
                $block->creatorObj->count_fields_updated_at,
                ['blocks' => DT::utcNow()]
            );
            $block->creatorObj->save();
        });
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
