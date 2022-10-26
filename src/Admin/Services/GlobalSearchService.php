<?php

namespace Aparlay\Core\Admin\Services;

use Aparlay\Chat\Admin\Models\Chat;
use Aparlay\Core\Admin\Models\User;
use Aparlay\Payout\Models\Order;
use Aparlay\Payout\Models\UserPayout;

class GlobalSearchService
{
    public static function search(string $query): array
    {
        return self::searchUsers($query)->take(10)->toArray();
    }

    private static function searchUsers(string $searchQuery)
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
                Order::query()->order($searchQuery)->get()
            );
            $chats = Chat::query()->participants($searchQuery)->limit(5)->get()->merge(
                Order::query()->order($searchQuery)->get()
            );
            $users = User::query()->user($searchQuery)->limit(5)->get()->merge(
                $users
            );
        }

        $result = [];

        $result[] = $users->map(function (User $user) {
            return [
                'category' => 'User',
                'model' => $user,
            ];
        });
        $result[] = $orders->map(function (Order $order) {
            return [
                'category' => 'Order',
                'model' => $order,
            ];
        });
        $result[] = $payouts->map(function (UserPayout $userPayout) {
            return [
                'category' => 'Payout',
                'model' => $userPayout,
            ];
        });
        $result[] = $chats->map(function (Chat $chat) {
            return [
                'category' => 'Chat',
                'model' => $chat,
            ];
        });

        return $result;
    }
}
