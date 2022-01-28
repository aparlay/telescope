<?php

namespace Aparlay\Core\Admin\Services;

use Aparlay\Core\Admin\Models\User;
use Aparlay\Core\Admin\Repositories\UserRepository;
use Aparlay\Core\Helpers\ActionButtonBladeComponent;
use Aparlay\Core\Jobs\DeleteAvatar;
use Aparlay\Core\Jobs\UploadAvatar;
use Aparlay\Core\Models\Enums\UserVerificationStatus;
use Illuminate\Support\Facades\Storage;

class UserService extends AdminBaseService
{
    protected UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository(new User());

        $this->filterableField = ['text_search', 'username', 'email', 'status', 'visibility', 'created_at'];
        $this->sorterableField = ['username', 'email', 'status', 'visibility', 'created_at'];
    }

    public function isModerationQueueNotEmpty()
    {
        return $this->userRepository->countPending() > 0;
    }

    public function firstPending()
    {
        $pendingUser = $this->userRepository->firstPending();
        $pendingUser->verification_status = UserVerificationStatus::UNDER_REVIEW->value;
        $pendingUser->save();

        return $pendingUser;
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
        $dateRangeFilter = null;
        $textSearch = '';

        if (! empty($filters)) {
            if (isset($filters['created_at'])) {
                $dateRangeFilter = $this->getDateRangeFilter($filters['created_at']);
                unset($filters['created_at']);
            }
            if (isset($filters['text_search'])) {
                $textSearch = $filters['text_search'];
                unset($filters['text_search']);
            }
            $users = $this->userRepository->getFilteredUser($textSearch, $filters, $offset, $limit, $sort, $dateRangeFilter);
        } else {
            $users = $this->userRepository->all($offset, $limit, $sort);
        }

        $this->appendAttributes($textSearch, $users, $filters, $dateRangeFilter);

        return $users;
    }

    /**
     * @param $users
     * @param $filters
     * @param $dateRangeFilter
     */
    public function appendAttributes($textSearch, $users, $filters, $dateRangeFilter)
    {
        $users->total_users = $this->userRepository->countCollection();
        $users->total_filtered_users = ! empty($filters) || ! empty($textSearch) || $dateRangeFilter ? $this->userRepository->countFilteredUser($textSearch, $filters, $dateRangeFilter) : $users->total_users;

        foreach ($users as $user) {
            $userBadges = [
                'status' => ActionButtonBladeComponent::getBadge($user->status_color, $user->status_name),
                'is_verified' => ActionButtonBladeComponent::getBadge($user->email_verified ? 'success' : 'danger', $user->email_verified ? 'Email Verified' : 'Email Not-verified'),
                'gender' => ActionButtonBladeComponent::getBadge($user->gender_color, $user->gender_name),
            ];

            $user->status_badge = implode('</br>', $userBadges);
            $user->action = ActionButtonBladeComponent::getViewActionButton($user->_id, 'user');
            $user->username_avatar = ActionButtonBladeComponent::getUsernameWithAvatar($user);
            $user->date_formatted = $user->created_at->toDateTimeString();
            $user->full_name = $user->full_name ?? ActionButtonBladeComponent::defaultValueNotSet();
            $user->follower_count = $user->follower_count ?? ActionButtonBladeComponent::defaultValueNotSet();
            $user->like_count = $user->like_count ?? ActionButtonBladeComponent::defaultValueNotSet();
            $user->media_count = $user->media_count ?? ActionButtonBladeComponent::defaultValueNotSet();
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

    public function update($user)
    {
        if (request()->hasFile('avatar')) {
            $this->uploadAvatar($user);
        }

        $data = request()->only([
            'username',
            'email',
            'bio',
            'gender',
            'interested_in',
            'type',
            'status',
            'visibility',
            'referral_id',
            'promo_link',
            'country_alpha2',
        ]);

        $dataBooleans = [
            'email_verified' => request()->boolean('email_verified'),
            'features' => [
                'tips' => request()->boolean('features.tips'),
                'demo' => request()->boolean('features.demo'),
            ],
        ];

        $data = array_merge($data, $dataBooleans);
        $role = request()?->input('role');
        if ($role && auth()->user()->hasRole(User::ROLE_SUPER_ADMINISTRATOR)) {
            $user->syncRoles(request()->input('role'));
        }

        $this->userRepository->update($data, $user->_id);
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
                $deleteOldFiles = new DeleteAvatar(basename($oldFileName));
                dispatch($deleteOldFiles->delay(100)->onQueue('low'));
            }
        }

        return false;
    }

    public function updateStatus($id): bool
    {
        return $this->userRepository->update(['status' => request()->input('status')], $id);
    }
}
