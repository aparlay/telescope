<?php

namespace Aparlay\Core\Admin\Services;

use Aparlay\Chat\Admin\Models\Chat;
use Aparlay\Core\Admin\Models\User;
use Aparlay\Payment\Models\Order;
use Aparlay\Payout\Models\UserPayout;

class GlobalSearchService
{
    public static function search(string $searchQuery): array
    {
        $orders = $payouts = $chats = [];
        $users = User::query()->textSearch($searchQuery)->limit(5)->get();

        if (filter_var($searchQuery, FILTER_VALIDATE_IP) !== false) {
            $users = User::query()->ip($searchQuery)->limit(5)->get()->merge(
                $users
            );
        }

        if (strlen($searchQuery) === 24 && strspn($searchQuery, '0123456789ABCDEFabcdef') === 24) {
            $orders = Order::query()->user($searchQuery)->limit(5)->get()->merge(
                Order::query()->order($searchQuery)->get()
            );
            $payouts = UserPayout::query()->user($searchQuery)->limit(5)->get()->merge(
                UserPayout::query()->userPayout($searchQuery)->get()
            );
            $chats = Chat::query()->participants($searchQuery)->limit(5)->get()->merge(
                Chat::query()->chat($searchQuery)->get()
            );
            $users = User::query()->user($searchQuery)->limit(5)->get()->merge(
                $users
            );
        }

        $result = [];

        $result['User'] = $users;
        $result['Order'] = $orders;
        $result['Payout'] = $payouts;
        $result['Chat'] = $chats;

        return $result;
    }
}
