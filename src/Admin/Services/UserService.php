<?php

namespace Aparlay\Core\Admin\Services;

use Aparlay\Core\Admin\Models\User;
use Aparlay\Core\Admin\Repositories\UserRepository;
use Aparlay\Core\Admin\Requests\UserGeneralUpdateRequest;
use Aparlay\Core\Admin\Requests\UserInfoUpdateRequest;
use Aparlay\Core\Admin\Requests\UserProfileUpdateRequest;
use Aparlay\Core\Api\V1\Traits\HasUserTrait;
use Aparlay\Core\Constants\Roles;
use Aparlay\Core\Events\UserStatusChangedEvent;
use Aparlay\Core\Jobs\DeleteAvatar;
use Aparlay\Core\Jobs\UploadAvatar;
use Aparlay\Core\Models\Enums\NoteType;
use Aparlay\Core\Models\Enums\UserStatus;
use Illuminate\Support\Facades\Storage;
use MongoDB\BSON\ObjectId;

class UserService extends AdminBaseService
{
    use HasUserTrait;
    protected UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository(new User());

        $this->filterableField = ['text_search', 'username', 'email', 'status', 'visibility', 'created_at'];
        $this->sorterableField = ['username', 'email', 'status', 'visibility', 'created_at'];
    }

    public function isModerationQueueNotEmpty()
    {
        return $this->userRepository->hasPending();
    }

    public function firstNextPending($currentUser, $userId)
    {
        $pendingUser = $this->userRepository->firstNextPending($userId);

        if ($pendingUser) {
            $this->userRepository->revertAllToPending($currentUser, $pendingUser);
            $pendingUser = $this->userRepository->setToUnderReview($pendingUser);
        }

        return $pendingUser;
    }

    public function firstPrevPending($currentUser, $userId)
    {
        $pendingUser = $this->userRepository->firstPrevPending($userId);

        if ($pendingUser) {
            $this->userRepository->revertAllToPending($currentUser, $pendingUser);
            $pendingUser = $this->userRepository->setToUnderReview($pendingUser);
        }

        return $pendingUser;
    }

    /**
     * @param $userId
     * @return bool
     */
    public function hasNextPending($userId): bool
    {
        return ! empty($this->userRepository->firstNextPending($userId));
    }

    /**
     * @param $userId
     * @return bool
     */
    public function hasPrevPending($userId): bool
    {
        return ! empty($this->userRepository->firstPrevPending($userId));
    }

    public function firstPending($user)
    {
        $underReviewExists = $this->userRepository->firstUnderReview($user);

        if ($underReviewExists) {
            return $underReviewExists;
        }

        $pendingUser = $this->userRepository->firstPending();

        if ($pendingUser) {
            $this->userRepository->revertAllToPending($user, $pendingUser);
            $pendingUser = $this->userRepository->setToUnderReview($pendingUser);
        }

        return  $pendingUser;
    }

    /**
     * @param $id
     * @return User|User[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
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

    public function updateProfile(User $user, UserProfileUpdateRequest $request): User
    {
        $data = $request->only([
            'username',
            'bio',
            'promo_link'
        ]);

        $role = $request->input('role');

        if ($role && auth()->user()->hasRole(Roles::SUPER_ADMINISTRATOR)) {
            $user->syncRoles($request->input('role'));
        }

        $user->fill($data);
        $user->save();

        return $user;
    }

    public function updateInfo(User $user, UserInfoUpdateRequest $request): User
    {
        if ($request->hasFile('avatar')) {
            $this->uploadAvatar($user);
        }

        $data = $request->only([
            'email',
            'gender',
            'interested_in',
            'type',
            'status',
            'visibility',
            'country_alpha2',
            'verification_status',
            'payout_country_alpha2',
        ]);

        $data['referral_id'] = $request->input('referral_id') ? new ObjectId($request->input('referral_id')) : null;

        $dataBooleans = [
            'email_verified' => $request->boolean('email_verified'),
        ];

        $data = array_merge($data, $dataBooleans);
        $role = $request->input('role');

        if ($role && auth()->user()->hasRole(Roles::SUPER_ADMINISTRATOR)) {
            $user->syncRoles($request->input('role'));
        }

        $user->fill($data);
        $user->save();

        return $user;
    }

    public function updateGeneral(User $user, UserGeneralUpdateRequest $request): User
    {
        if ($request->hasFile('avatar')) {
            $this->uploadAvatar($user);
        }

        $data = [
            'features' => [
                'tips' => $request->boolean('features.tips'),
                'demo' => $request->boolean('features.demo'),
            ],
        ];

        $role = $request->input('role');

        if ($role && auth()->user()->hasRole(Roles::SUPER_ADMINISTRATOR)) {
            $user->syncRoles($request->input('role'));
        }

        $user->fill($data);
        $user->save();

        return $user;
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
                UploadAvatar::dispatch((string) $user->_id, 'avatars/'.$avatar)->delay(10);
            }

            if (! str_contains($oldFileName, 'default_')) {
                DeleteAvatar::dispatch(basename($oldFileName))->delay(100)->onQueue('low');
            }
        }

        return false;
    }

    public function updateStatus($id, $userStatus): bool
    {
        $user = $this->find($id);

        $noteType = match ($userStatus) {
            UserStatus::SUSPENDED->value => NoteType::SUSPEND->value,
            UserStatus::BLOCKED->value => NoteType::BAN->value,
            UserStatus::ACTIVE->value => match ($user->status) {
                UserStatus::SUSPENDED->value => NoteType::UNSUSPEND->value,
                UserStatus::BLOCKED->value => NoteType::UNBAN->value,
                default => null
            },
            default => null
        };

        if ($noteType) {
            UserStatusChangedEvent::dispatch($this->getUser(), $user, $noteType);
        }

        return $this->userRepository->update(['status' => $userStatus], $id);
    }
}
