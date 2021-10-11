<?php

namespace Aparlay\Core\Admin\Services;

use Aparlay\Core\Admin\Models\User;
use Aparlay\Core\Admin\Repositories\UserRepository;

class UserService extends AdminBaseService
{
    protected UserRepository $userRepository;

    protected $filterableFields = ['username', 'email', 'full_name', 'status', 'visibility', 'follower_count', 'like_count', 'media_count'];

    public function __construct()
    {
        $this->userRepository = new UserRepository(new User());
    }

    public function getUsers()
    {
        $users = $this->userRepository->all();

        $this->appendBadges($users);

        return $users;
    }

    public function getFilteredUsers()
    {
        $fields = request()->UserSearch ?? [];

        $filters = [];
        if (! empty($fields)) {
            $filters = $this->cleanFilterFields($fields, $this->filterableFields);
        }

        if (! empty($filters)) {
            $users = $this->userRepository->getFilteredUsers($filters);

            $this->appendBadges($users);

            return $users;
        } else {
            return $this->getUsers();
        }
    }

    /**
     * @param $id
     */
    public function find($id)
    {
        $user = $this->userRepository->find($id);

        $statusBadge = [
            'status' => $user->status_name,
            'color' => $user->status_color,
        ];

        $user->status_badge = $statusBadge;

        return $user;
    }

    public function appendBadges($users)
    {
        foreach ($users as $user) {
            $statusBadge = [
                'status' => $user->status_name,
                'color' => $user->status_color,
            ];

            $genderBadge = [
                'gender' => $user->gender_name,
                'color' => $user->gender_color,
            ];

            $isVerifiedBadge = [
                'is_verified' => $user->email_verified ? 'Email Verified' : 'Email Not-verified',
                'color' => $user->email_verified ? 'success' : 'danger',
            ];

            $user->status_badge = $statusBadge;
            $user->gender_badge = $genderBadge;
            $user->isverified_badge = $isVerifiedBadge;
        }
    }

    public function getUserStatuses()
    {
        return $this->userRepository->getUserStatues();
    }
}
