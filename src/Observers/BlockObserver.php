<?php

namespace Aparlay\Core\Observers;

use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Models\Block;
use Aparlay\Core\Models\Follow;
use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\MediaLike;
use Aparlay\Core\Models\User;
use MongoDB\BSON\ObjectId;

class BlockObserver extends BaseModelObserver
{
    /**
     * Handle the Block "creating" event.
     *
     * @param  Block  $model
     * @return void
     */
    public function creating($model): void
    {
        $user = User::user($model->user['_id'])->first();
        $creator = User::user($model->creator['_id'])->first();

        $model->user = [
            '_id' => new ObjectId($user->_id),
            'username' => $user->username,
            'avatar' => $user->avatar,
        ];

        $model->creator = [
            '_id' => new ObjectId($creator->_id),
            'username' => $creator->username,
            'avatar' => $creator->avatar,
        ];

        parent::creating($model);
    }

    /**
     * Handle the Block "created" event.
     *
     * @param  Block  $model
     * @return void
     */
    public function created($model): void
    {
        $blockCount = Block::creator($model->creator['_id'])->count();
        $model->creatorObj->addToSet('blocks', [
            '_id' => new ObjectId($model->user['_id']),
            'username' => $model->user['username'],
            'avatar' => $model->user['avatar'],
        ]);
        $model->creatorObj->count_fields_updated_at = array_merge(
            $model->creatorObj->count_fields_updated_at,
            ['blocks' => DT::utcNow()]
        );
        $stats = $model->creatorObj->stats;
        $stats['counters']['blocks'] = $blockCount;
        $model->creatorObj->stats = $stats;
        $model->creatorObj->save();

        if (($follow = Follow::creator($model->creator['_id'])->user($model->user['_id'])->first()) !== null) {
            $follow->delete();
        }

        if (($follow = Follow::creator($model->user['_id'])->user($model->creator['_id'])->first()) !== null) {
            $follow->delete();
        }

        foreach (Media::creator($model->user['_id'])->get() as $media) {
            $media->addToSet('blocked_user_ids', new ObjectId($model->creator['_id']));
            $media->save();
        }

        foreach (Media::creator($model->creator['_id'])->get() as $media) {
            $media->addToSet('blocked_user_ids', new ObjectId($model->user['_id']));
            $media->save();
        }

        foreach (MediaLike::creator($model->user['_id'])->user($model->creator['_id'])->get() as $mediaLike) {
            $mediaLike->delete();
        }

        foreach (MediaLike::creator($model->creator['_id'])->user($model->user['_id'])->get() as $mediaLike) {
            $mediaLike->delete();
        }
    }

    /**
     * Handle the Block "deleted" event.
     *
     * @param  Block  $model
     * @return void
     */
    public function deleted($model): void
    {
        $blockCount = Block::creator($model->creator['_id'])->count();
        $model->creatorObj->block_count = $blockCount;
        $model->creatorObj->removeFromSet('blocks', [
            '_id' => new ObjectId($model->user['_id']),
            'username' => $model->user['username'],
            'avatar' => $model->user['avatar'],
        ]);
        $model->creatorObj->count_fields_updated_at = array_merge(
            $model->creatorObj->count_fields_updated_at,
            ['blocks' => DT::utcNow()]
        );
        $stats = $model->creatorObj->stats;
        $stats['counters']['blocks'] = $blockCount;
        $model->creatorObj->stats = $stats;
        $model->creatorObj->save();

        foreach (Media::creator($model->user['_id'])->get() as $media) {
            $media->removeFromSet('blocked_user_ids', new ObjectId($model->creator['_id']));
            $media->save();
        }

        foreach (Media::creator($model->creator['_id'])->get() as $media) {
            $media->removeFromSet('blocked_user_ids', new ObjectId($model->user['_id']));
            $media->save();
        }
    }
}
