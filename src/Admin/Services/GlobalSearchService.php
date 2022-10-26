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
        return User::query()
            ->textSearch($searchQuery)
            ->get()
            ->map(function (User $user) {
                return [
                    'category' => 'User',
                    'link' => $user->admin_url,
                    'title' => $user->full_name,
                ];
            });
    }
}
