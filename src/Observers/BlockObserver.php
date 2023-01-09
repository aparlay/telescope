<?php

namespace Aparlay\Core\Observers;

use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Jobs\BlockedUserBlockMedia;
use Aparlay\Core\Jobs\BlockedUserDeleteFollow;
use Aparlay\Core\Jobs\BlockedUserDeleteMediaLikes;
use Aparlay\Core\Jobs\UnBlockedUserUnBlockMedia;
use Aparlay\Core\Models\Block;
use Aparlay\Core\Models\User;
use Laravel\Octane\Facades\Octane;
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
        if (! isset($model->user['username'], $model->user['avatar'])) {
            $user = User::user($model->user['_id'])->first();
            $model->user = [
                '_id' => new ObjectId($user->_id),
                'username' => $user->username,
                'avatar' => $user->avatar,
            ];
        }

        if (! isset($model->creator['username'], $model->creator['avatar'])) {
            $creator = User::user($model->creator['_id'])->first();
            $model->creator = [
                '_id' => new ObjectId($creator->_id),
                'username' => $creator->username,
                'avatar' => $creator->avatar,
            ];
        }

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
        $blockCount = Block::query()->creator($model->creator['_id'])->count();
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

        Octane::concurrently([
            fn () => BlockedUserBlockMedia::dispatch((string) $model->creator['_id'], (string) $model->user['_id']),
            fn () => BlockedUserDeleteFollow::dispatch((string) $model->creator['_id'], (string) $model->user['_id']),
            fn () => BlockedUserDeleteFollow::dispatch((string) $model->user['_id'], (string) $model->creator['_id']),
            fn () => BlockedUserDeleteMediaLikes::dispatch((string) $model->creator['_id'], (string) $model->user['_id']),
            fn () => BlockedUserDeleteMediaLikes::dispatch((string) $model->user['_id'], (string) $model->creator['_id']),
        ], 5000);
    }

    /**
     * Handle the Block "deleted" event.
     *
     * @param  Block  $model
     * @return void
     */
    public function deleted($model): void
    {
        $blockCount = Block::query()->creator($model->creator['_id'])->count();
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

        UnBlockedUserUnBlockMedia::dispatch((string) $model->creator['_id'], (string) $model->user['_id']);
    }
}
