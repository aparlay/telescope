<?php

namespace Aparlay\Core\Admin\Services;

use Aparlay\Core\Admin\Models\User;

class GlobalSearchService
{
    public static function search(string $query): array
    {
        return self::searchUsers($query)->take(7)->toArray();
    }

    private static function searchUsers(string $searchQuery)
    {
        if (str_starts_with('@', $searchQuery)) {
            $users = User::query()->username($searchQuery)->get();
        }

        if (strpos('@', $searchQuery) > 0) {
            $users = User::query()->email($searchQuery)->get();
        }

        if (!str_contains('@', $searchQuery)) {
            $users = User::query()->textSearch($searchQuery)->get();
        }

        if(filter_var($searchQuery, FILTER_VALIDATE_IP) !== false) {
            $users = User::query()->textSearch($searchQuery)->get();
        }

        if (!empty($users)) {
            $result['users'] = $users->map(function (User $user) {
                return [
                    'category' => 'User',
                    'link' => $user->admin_url,
                    'title' => $user->full_name ?? $user->username,
                ];
            });
        }

        return $users->map(function (User $user) {
                return [
                    'category' => 'User',
                    'link' => $user->admin_url,
                    'title' => $user->full_name ?? $user->username,
                ];
            });
    }
}
