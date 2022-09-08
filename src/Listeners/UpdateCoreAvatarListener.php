<?php

namespace Aparlay\Core\Listeners;

use Aparlay\Core\Events\UserAvatarChangedEvent;
use Aparlay\Core\Helpers\Cdn;
use Aparlay\Core\Models\Block;
use Aparlay\Core\Models\Follow;
use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\MediaLike;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class UpdateCoreAvatarListener implements ShouldQueue
{
    public function handle(UserAvatarChangedEvent $event)
    {
        $avatar = $event->user->avatar;
        Media::creator($event->user->_id)->update(['creator.avatar' => $avatar]);
        Follow::creator($event->user->_id)->update(['creator.avatar' => $avatar]);
        Follow::user($event->user->_id)->update(['user.avatar' => $avatar]);
        Block::query()->creator($event->user->_id)->update(['creator.avatar' => $avatar]);
        Block::query()->user($event->user->_id)->update(['user.avatar' => $avatar]);
        MediaLike::query()->creator($event->user->_id)->update(['creator.avatar' => $avatar]);

        $cacheKey = 'SimpleUserCast:'.$event->user->_id;
        $userArray = [
            '_id' => (string) $event->user->_id,
            'username' => $event->user->username,
            'avatar' => $event->user->avatar ?? Cdn::avatar('default.jpg'),
            'is_verified' => $event->user->is_verified,
        ];

        Cache::store('octane')->put($cacheKey, json_encode($userArray), config('app.cache.veryLongDuration'));
        Redis::set($cacheKey, $userArray, config('app.cache.veryLongDuration'));
    }
}
