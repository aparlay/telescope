<?php

namespace Aparlay\Core\Api\V1\Services;

use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Repositories\UserRepository;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class OnlineUserService
{
    protected UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository(new User());
    }

    /**
     * online user implementation with redis sets
     * three category will be created
     * - all for the content creators who choose show online status to all
     * - follower for the content creators who choose show online status for followers only
     * - none for admin panel online users.
     *
     * this patter will create two sets for each 5 minutes and 10 minutes 10:00-10:05 and 10:05-10:10
     * each authenticated request check and add user to both if user
     * doesn't send any request for more than 5 minutes her account won't add to the next windows
     * and considered as offline after at most 5 minutes of inactivity
     *
     * @return array
     */
    public static function timeWindows(): array
    {
        $currentMinute = now()->minute;
        $currentMinuteWindow = $currentMinute - ($currentMinute % 5);
        $currentWindow = now()->hour.Str::padLeft($currentMinuteWindow, 2, '0');

        $nextMinuteWindow = $currentMinuteWindow + 5;
        $nextHourWindow = now()->hour;
        if ($nextMinuteWindow == 60) {
            $nextMinuteWindow = '00';
            $nextHourWindow = now()->addHour()->hour;
        }
        $nextWindow = $nextHourWindow.Str::padLeft($nextMinuteWindow, 2, '0');

        return [$currentWindow, $nextWindow];
    }

    /**
     * online user implementation with redis sets
     * three category will be created
     * - all for the content creators who choose show online status to all
     * - follower for the content creators who choose show online status for followers only
     * - none for admin panel online users.
     *
     * this patter will create two sets for each 5 minutes and 10 minutes 10:00-10:05 and 10:05-10:10
     * each authenticated request check and add user to both if user
     * doesn't send any request for more than 5 minutes her account won't add to the next windows
     * and considered as offline after at most 5 minutes of inactivity
     *
     * @param  User|Authenticatable  $user
     * @return void
     */
    public function online(User|Authenticatable $user)
    {
        [$currentWindow, $nextWindow] = self::timeWindows();

        $onlineAllCurrent = config('app.cache.keys.online.all').':'.$currentWindow;
        $onlineFollowingsCurrent = config('app.cache.keys.online.followings').':'.$currentWindow;
        $onlineNoneCurrent = config('app.cache.keys.online.none').':'.$currentWindow;
        $onlineAllNext = config('app.cache.keys.online.all').':'.$nextWindow;
        $onlineFollowingsNext = config('app.cache.keys.online.followings').':'.$nextWindow;
        $onlineNoneNext = config('app.cache.keys.online.none').':'.$nextWindow;

        $now = time();
        $currentWindowExpireAt = ceil($now / 300) * 300;
        $nextWindowExpireAt = ceil($now / 600) * 600;
        if ((int)Redis::sCard($onlineNoneCurrent) === 0) {
            Redis::expireAt($onlineAllCurrent, $currentWindowExpireAt);
            Redis::expireAt($onlineFollowingsCurrent, $currentWindowExpireAt);
            Redis::expireAt($onlineNoneCurrent, $currentWindowExpireAt);
        }

        if ((int)Redis::sCard($onlineNoneNext) === 0) {
            Redis::expireAt($onlineAllNext, $nextWindowExpireAt);
            Redis::expireAt($onlineFollowingsNext, $nextWindowExpireAt);
            Redis::expireAt($onlineNoneNext, $nextWindowExpireAt);
        }

        switch ($user->show_online_status) {
            case \Aparlay\Core\Models\User::SHOW_ONLINE_STATUS_ALL:
                Redis::sAdd($onlineAllCurrent, (string) $user->_id);
                Redis::sAdd($onlineAllNext, (string) $user->_id);
                Redis::sAdd($onlineFollowingsCurrent, (string) $user->_id);
                Redis::sAdd($onlineFollowingsNext, (string) $user->_id);
                Redis::sAdd($onlineNoneCurrent, (string) $user->_id);
                Redis::sAdd($onlineNoneNext, (string) $user->_id);
                break;
            case \Aparlay\Core\Models\User::SHOW_ONLINE_STATUS_FOLLOWERS:
                Redis::sAdd($onlineFollowingsCurrent, (string) $user->_id);
                Redis::sAdd($onlineFollowingsNext, (string) $user->_id);
                Redis::sAdd($onlineNoneCurrent, (string) $user->_id);
                Redis::sAdd($onlineNoneNext, (string) $user->_id);
                break;
            default:
                Redis::sAdd($onlineNoneCurrent, (string) $user->_id);
                Redis::sAdd($onlineNoneNext, (string) $user->_id);
        }
    }

    public function offline(User|Authenticatable $user)
    {
        [$currentWindow, $nextWindow] = self::timeWindows();

        $onlineAllCurrent = config('app.cache.keys.online.all').':'.$currentWindow;
        $onlineFollowingsCurrent = config('app.cache.keys.online.followings').':'.$currentWindow;
        $onlineNoneCurrent = config('app.cache.keys.online.none').':'.$currentWindow;
        $onlineAllNext = config('app.cache.keys.online.all').':'.$nextWindow;
        $onlineFollowingsNext = config('app.cache.keys.online.followings').':'.$nextWindow;
        $onlineNoneNext = config('app.cache.keys.online.none').':'.$nextWindow;

        switch ($user->show_online_status) {
            case \Aparlay\Core\Models\User::SHOW_ONLINE_STATUS_ALL:
                Redis::sRem($onlineAllCurrent, (string) $user->_id);
                Redis::sRem($onlineAllNext, (string) $user->_id);
                Redis::sRem($onlineFollowingsCurrent, (string) $user->_id);
                Redis::sRem($onlineFollowingsNext, (string) $user->_id);
                Redis::sRem($onlineNoneCurrent, (string) $user->_id);
                Redis::sRem($onlineNoneNext, (string) $user->_id);
                break;
            case \Aparlay\Core\Models\User::SHOW_ONLINE_STATUS_FOLLOWERS:
                Redis::sRem($onlineFollowingsCurrent, (string) $user->_id);
                Redis::sRem($onlineFollowingsNext, (string) $user->_id);
                Redis::sRem($onlineNoneCurrent, (string) $user->_id);
                Redis::sRem($onlineNoneNext, (string) $user->_id);
                break;
            default:
                Redis::sRem($onlineNoneCurrent, (string) $user->_id);
                Redis::sRem($onlineNoneNext, (string) $user->_id);
        }
    }
}
