<?php

namespace Aparlay\Core\Admin\Services;

use Aparlay\Chat\Admin\Models\Chat;
use Aparlay\Core\Admin\Models\Media;
use Aparlay\Core\Admin\Models\User;
use Aparlay\Payment\Models\Order;
use Aparlay\Payout\Models\UserPayout;
use Illuminate\Support\Collection;

class GlobalSearchService
{
    public static function search(string $searchQuery): array
    {
        $orders           = $payouts = $chats = $medias = [];

        try {
            $users = self::searchUsers($searchQuery);
        } catch (\Throwable) {
            $users = [];
        }

        if (strlen($searchQuery) === 24 && strspn($searchQuery, '0123456789ABCDEFabcdef') === 24) {
            $orders  = Order::query()->user($searchQuery)->limit(5)->get()->merge(
                Order::query()->order($searchQuery)->get()
            );
            $payouts = UserPayout::query()->user($searchQuery)->limit(5)->get()->merge(
                UserPayout::query()->userPayout($searchQuery)->get()
            );
            $chats   = Chat::query()->participants($searchQuery)->limit(5)->get()->merge(
                Chat::query()->chat($searchQuery)->get()
            );
            $users   = User::query()->user($searchQuery)->limit(5)->get()->merge(
                $users
            );
            $medias  = Media::query()->media($searchQuery)->limit(5)->get();
        }

        if (strlen($searchQuery) === 6) {
            $medias = Media::query()->slug($searchQuery)->limit(5)->get();
        }

        $result           = [];

        $result['User']   = $users;
        $result['Order']  = $orders;
        $result['Payout'] = $payouts;
        $result['Chat']   = $chats;
        $result['Media']  = $medias;

        return $result;
    }

    private static function searchUsers(string $searchQuery): Collection
    {
        if (mb_substr($searchQuery, 0, 1) === '@') {
            return User::query()->where('username', 'LIKE', trim($searchQuery, '@') . '%')->limit(5)->get();
        }

        $users = User::query()->textSearch($searchQuery)->limit(5)->get();

        if (filter_var($searchQuery, FILTER_VALIDATE_IP) !== false) {
            $users = User::query()->ip($searchQuery)->limit(5)->get()->merge(
                $users
            );
        }

        return $users;
    }
}
