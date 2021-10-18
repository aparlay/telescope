<?php

namespace Aparlay\Core\Admin\Services;

use Aparlay\Core\Admin\Models\User;
use Aparlay\Core\Admin\Repositories\UserRepository;
use Aparlay\Core\Helpers\ActionButtonBladeComponent;

class UserService extends AdminBaseService
{
    protected UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository(new User());

        $this->filterableField = ['username', 'email', 'status', 'visibility'];
        $this->sorterableField = ['username', 'email', 'status', 'visibility', 'created_at'];
    }

    /**
     * @return mixed
     */
    public function getFilteredUsers(): mixed
    {
        $offset = (int) request()->get('start');
        $limit = (int) request()->get('length');

        $filters = $this->getFilters();
        $sort = $this->tableSort();
        if (! empty($filters)) {
            $users = $this->userRepository->getFilteredUser($offset, $limit, $sort, $filters);
        } else {
            $users = $this->userRepository->all($offset, $limit, $sort);
        }

        $this->appendAttributes($users, $filters);

        return $users;
    }

    /**
     * @param $users
     * @param $filters
     */
    public function appendAttributes($users, $filters)
    {
        $users->total_users = $this->userRepository->countCollection();
        $users->total_filtered_users = ! empty($filters) ? $this->userRepository->countFilteredUser($filters) : $users->total_users;

        foreach ($users as $user) {
            $userBadges = [
                'status' => ActionButtonBladeComponent::getBadge($user->status_color, $user->status_name),
                'is_verified' => ActionButtonBladeComponent::getBadge($user->email_verified ? 'success' : 'danger', $user->email_verified ? 'Email Verified' : 'Email Not-verified'),
                'gender' => ActionButtonBladeComponent::getBadge($user->gender_color, $user->gender_name),
            ];

            $user->status_badge = implode('</br>', $userBadges);
            $user->action = ActionButtonBladeComponent::getViewActionButton($user->_id, 'user');
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

    /**
     * @return array
     */
    public function getUserStatuses(): array
    {
        return $this->userRepository->getUserStatuses();
    }

    /**
     * @return array
     */
    public function getVisibilities(): array
    {
        return $this->userRepository->getVisibilities();
    }
}
