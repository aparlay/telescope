<?php

namespace Aparlay\Core\Observers;

use Aparlay\Core\Api\V1\Models\Block;
use Aparlay\Core\Api\V1\Models\Follow;
use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Models\MediaLike;
use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Helpers\DT;
use MongoDB\BSON\ObjectId;

class BlockObserver
{
    /**
     * Handle the Block "creating" event.
     *
     * @param  Block  $block
     * @return void
     */
    public function creating(Block $block)
    {
        $user = User::user($block->user['_id'])->first();
        $creator = User::user($block->creator['_id'])->first();

        $block->user = [
            '_id' => new ObjectId($user->_id),
            'username' => $user->username,
            'avatar' => $user->avatar,
        ];

        $block->creator = [
            '_id' => new ObjectId($creator->_id),
            'username' => $creator->username,
            'avatar' => $creator->avatar,
        ];
    }

    /**
     * Handle the Block "created" event.
     *
     * @param  Block  $block
     * @return void
     */
    public function created(Block $block)
    {
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
            $media->save();
        }

        foreach (Media::creator($block->creator['_id'])->get() as $media) {
            $media->addToSet('blocked_user_ids', new ObjectId($block->user['_id']));
            $media->save();
        }

        foreach (MediaLike::creator($block->user['_id'])->user($block->creator['_id'])->get() as $mediaLike) {
            $mediaLike->delete();
        }

        foreach (MediaLike::creator($block->creator['_id'])->user($block->user['_id'])->get() as $mediaLike) {
            $mediaLike->delete();
        }
    }

    /**
     * Handle the Block "updated" event.
     *
     * @param  Block  $block
     * @return void
     */
    public function updated(Block $block)
    {
        //
    }

    /**
     * Handle the Block "deleted" event.
     *
     * @param  Block  $block
     * @return void
     */
    public function deleted(Block $block)
    {
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
            $media->removeFromSet('blocked_user_ids', new ObjectId($block->creator['_id']));
            $media->save();
        }

        foreach (Media::creator($block->creator['_id'])->get() as $media) {
            $media->removeFromSet('blocked_user_ids', new ObjectId($block->user['_id']));
            $media->save();
        }
    }

    /**
     * Handle the Block "restored" event.
     *
     * @param  Block  $block
     * @return void
     */
    public function restored(Block $block)
    {
        //
    }

    /**
     * Handle the Block "force deleted" event.
     *
     * @param  Block  $block
     * @return void
     */
    public function forceDeleted(Block $block)
    {
        //
    }
}
