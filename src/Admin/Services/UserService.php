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

    public function getUserFilter()
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

            $this->appendBadges($users);

            return $users;
        } else {
            return $this->getUsers();
        }
    }

    public function appendBadges($users)
    {
        foreach ($users as $user) {
            $statusBadge = [
                'status' => $user->getStatuses()[$user->status],
                'color' => $user->getStatusColor(),
            ];

            $genderBadge = [
                'gender' => $user->getGenders()[$user->gender],
                'color' => $user->getGenderColor(),
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
