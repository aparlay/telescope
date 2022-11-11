<?php

namespace Aparlay\Core\Listeners;

use Aparlay\Core\Events\AvatarChangedEvent;
use Aparlay\Core\Events\UsernameChangedEvent;
use Aparlay\Core\Helpers\Cdn;
use Aparlay\Core\Models\Block;
use Aparlay\Core\Models\Follow;
use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\MediaComment;
use Aparlay\Core\Models\MediaCommentLike;
use Aparlay\Core\Models\MediaLike;
use Aparlay\Core\Models\UserNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Redis;

class UpdateCoreSimpleUserListener implements ShouldQueue
{
    public function handle(UsernameChangedEvent|AvatarChangedEvent $event)
    {
        $user = $event->user;

        Follow::query()->creator($user->_id)->update(['creator.avatar' => $user->avatar, 'creator.username' => $user->username]);
        Follow::query()->user($user->_id)->update(['user.avatar' => $user->avatar, 'user.username' => $user->username]);
        Block::query()->creator($user->_id)->update(['creator.avatar' => $user->avatar, 'creator.username' => $user->username]);
        Block::query()->user($user->_id)->update(['user.avatar' => $user->avatar, 'user.username' => $user->username]);
        Media::creator($user->_id)->update(['creator.avatar' => $user->avatar, 'creator.username' => $user->username]);
        MediaLike::query()->creator($user->_id)->update(['creator.avatar' => $user->avatar, 'creator.username' => $user->username]);
        MediaComment::query()->creator($user->_id)->update(['creator.avatar' => $user->avatar, 'creator.username' => $user->username]);
        MediaCommentLike::query()->creator($user->_id)->update(['creator.avatar' => $user->avatar, 'creator.username' => $user->username]);
        UserNotification::query()->actor((string) $user->_id)->update(['payload.user.avatar' => $user->avatar, 'payload.user.username' => $user->username]);

        $cacheKey = 'SimpleUserCast:'.$user->_id;
        $userArray = [
            '_id' => (string) $user->_id,
            'username' => $user->username,
            'avatar' => $user->avatar ?? Cdn::avatar('default.jpg'),
            'is_verified' => $user->is_verified,
        ];

        Redis::unlink($cacheKey);
        Redis::set($cacheKey, $userArray, config('app.cache.veryLongDuration'));
    }
}
