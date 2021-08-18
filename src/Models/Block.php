<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Database\Factories\BlockFactory;
use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Models\Scopes\BlockScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use MongoDB\BSON\ObjectId;

/**
 * Class Block.
 *
 * @property ObjectId   $_id
 * @property string     $hashtag
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
 * @method static |self|Builder isDeleted()                      get deleted blocks
 * @method static |self|Builder isNotDeleted()                   get not deleted blocks
 * @method static |self|Builder creator(ObjectId|string $userId) get creator user
 * @method static |self|Builder user(ObjectId|string $userId)    get blocked user
 */
class Block extends Model
{
    use HasFactory;
    use Notifiable;
    use BlockScope;

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
                'avatar' => $block->user['avatar'],
            ]);
            $block->creatorObj->count_fields_updated_at = array_merge(
                $block->creatorObj->count_fields_updated_at,
                ['blocks' => DT::utcNow()]
            );
            $block->creatorObj->save();

            if (($follow = Follow::creator($block->creator['_id'])->user($block->user['_id'])->first()) !== null) {
                $follow->delete();
            }

            if (($follow = Follow::creator($block->user['_id'])->user($block->creator['_id'])->first()) !== null) {
                $follow->delete();
            }

            foreach (Media::creator($block->user['_id'])->get() as $media) {
                $media->addToSet('blocked_user_ids', new ObjectId($block->creator['_id']));
            }

            foreach (Media::creator($block->creator['_id'])->get() as $media) {
                $media->addToSet('blocked_user_ids', new ObjectId($block->user['_id']));
            }

            foreach (MediaLike::creator($block->user['_id'])->user($block->creator['_id'])->get() as $mediaLike) {
                $mediaLike->delete();
            }

            foreach (MediaLike::creator($block->creator['_id'])->user($block->user['_id'])->get() as $mediaLike) {
                $mediaLike->delete();
            }
        });

        static::deleted(function ($block) {
            $block->creatorObj->block_count--;
            $block->creatorObj->removeFromSet('blocks', [
                '_id' => new ObjectId($block->user['_id']),
                'username' => $block->user['username'],
                'avatar' => $block->user['avatar'],
            ]);
            $block->creatorObj->count_fields_updated_at = array_merge(
                $block->creatorObj->count_fields_updated_at,
                ['blocks' => DT::utcNow()]
            );
            $block->creatorObj->save();

            foreach (Media::creator($block->user['_id'])->get() as $media) {
                $media->removeFromSet('blocked_user_ids', $this->creator_id);
            }

            foreach (Media::creator($block->creator['_id'])->get() as $media) {
                $media->removeFromSet('blocked_user_ids', $this->user_id);
            }
        });
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return BlockFactory::new();
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
