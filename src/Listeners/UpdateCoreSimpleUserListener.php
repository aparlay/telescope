<?php

namespace Aparlay\Core\Listeners;

use Aparlay\Core\Casts\SimpleUserCast;
use Aparlay\Core\Events\AvatarChangedEvent;
use Aparlay\Core\Events\UsernameChangedEvent;
use Aparlay\Core\Models\Block;
use Aparlay\Core\Models\Email;
use Aparlay\Core\Models\Follow;
use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\MediaComment;
use Aparlay\Core\Models\MediaCommentLike;
use Aparlay\Core\Models\MediaLike;
use Aparlay\Core\Models\UserNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

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
        UserNotification::query()->payloadUserId((string) $user->_id)->update(['payload.user.avatar' => $user->avatar, 'payload.user.username' => $user->username]);
        Email::query()->user((string) $user->_id)->update(['user.avatar' => $user->avatar, 'user.username' => $user->username]);

        SimpleUserCast::cacheByUserId($user->_id, true);
    }
}
