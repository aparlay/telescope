<?php

namespace Aparlay\Core\Admin\Services;

use Aparlay\Core\Admin\Models\User;
use Aparlay\Core\Admin\Repositories\UserRepository;
use Aparlay\Core\Helpers\ActionButtonBladeComponent;
use Aparlay\Core\Jobs\DeleteAvatar;
use Aparlay\Core\Jobs\UploadAvatar;
use Illuminate\Support\Facades\Storage;

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

    public function update($id)
    {
        $user = $this->userRepository->find($id);

        if(request()->hasFile('avatar')) {
            $this->uploadAvatar($user);
        }

        request()->request->add([
            'email_verified' => request()->input('email_verified') == 'on',
            'features' => [
                'tips' => request()->input('features.tips') == 'on',
                'demo' => request()->input('features.demo') == 'on'
            ],
        ]);

        return $this->userRepository->update(request()->except(['_token', '_method', 'avatar']), $id);
    }

    public function uploadAvatar($user): bool
    {
        if (! request()->hasFile('avatar') && ! request()->file('avatar')->isValid()) {
            return false;
        }

        $extension = request()->avatar->getClientOriginalExtension();
        $avatar = uniqid((string) $user->_id, false).'.'.$extension;

        if ((request()->avatar->storePubliclyAs('avatars', $avatar, 'public')) !== false) {
            /* Store avatar name in database */
            $oldFileName = $user->avatar;
            $this->userRepository->update(['avatar' => Storage::disk('public')->url('avatars/'.$avatar)], $user->_id);

            if (! config('app.is_testing')) {
                dispatch((new UploadAvatar((string) $user->_id, 'avatars/'.$avatar))->delay(10)->onQueue('low'));
            }

            if (! str_contains($oldFileName, 'default_')) {
                $deleteOldFiles = new DeleteAvatar((string) $user->_id, basename($oldFileName));
                dispatch($deleteOldFiles->delay(100)->onQueue('low'));
            }
        }

        return false;
    }
}
