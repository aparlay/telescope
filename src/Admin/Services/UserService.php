<?php

namespace Aparlay\Core\Admin\Services;

use Aparlay\Core\Admin\Models\User;
use Aparlay\Core\Admin\Repositories\UserRepository;
use MongoDB\BSON\Regex;
use WdevRs\LaravelDatagrid\DataGrid\DataGrid;

class UserService extends DataGrid
{
    protected UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository(new User());
    }

    public function getUsers()
    {
        $users = $this->userRepository->getUsers();

        $this->appendBadges($users);

        return $users;
    }

    public function getFilteredUsers()
    {
        $username = request()->username ?? null;
        $email = request()->email ?? null;

        if ($username || $email) {
            $query = User::query();

            if ($username) {
                $query = $query->where('username', 'regex', new Regex('^'.$username));
            }

            if ($email) {
                $query = $query->orWhere('email', 'regex', new Regex('^'.$email));
            }
            $users = $query->paginate(20);

            return $users;
        } else {
            return $this->getUsers();
        }
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
}
