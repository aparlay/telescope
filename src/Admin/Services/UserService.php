<?php

namespace Aparlay\Core\Admin\Services;

use Aparlay\Core\Admin\Models\User;
use Aparlay\Core\Admin\Repositories\UserRepository;
use Aparlay\Core\Admin\Requests\UserGeneralUpdateRequest;
use Aparlay\Core\Admin\Requests\UserInfoUpdateRequest;
use Aparlay\Core\Admin\Requests\UserPayoutsUpdateRequest;
use Aparlay\Core\Admin\Requests\UserProfileUpdateRequest;
use Aparlay\Core\Api\V1\Traits\HasUserTrait;
use Aparlay\Core\Constants\Roles;
use Aparlay\Core\Events\UserPasswordChangedEvent;
use Aparlay\Core\Events\UserSettingChangedEvent;
use Aparlay\Core\Events\UserStatusChangedEvent;
use Aparlay\Core\Events\UserVisibilityChangedEvent;
use Aparlay\Core\Jobs\DeleteAvatar;
use Aparlay\Core\Jobs\DeleteMediaMetadata;
use Aparlay\Core\Jobs\UploadAvatar;
use Aparlay\Core\Models\Enums\NoteType;
use Aparlay\Core\Models\Enums\UserStatus;
use Aparlay\Core\Models\Enums\UserType;
use Aparlay\Core\Models\Enums\UserVisibility;
use Hash;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;

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
            'promo_link',
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
        $data = $request->only([
            'email',
            'gender',
            'type',
            'birthday',
            'status',
            'visibility',
            'country_alpha2',
            'verification_status',
            'payout_country_alpha2',
            'full_name',
        ]);

        $dataBooleans = [
            'email_verified' => $request->boolean('email_verified'),
            'visibility' => (int) $request->boolean('visibility'),
        ];

        $data = array_merge($data, $dataBooleans);

        if (auth()->user()->hasRole(Roles::SUPER_ADMINISTRATOR)) {
            $role = $request->input('role');
            $originalRole = $user->roles()->first();
            $user->type = $role ? UserType::ADMIN->value : UserType::USER->value;
            $user->syncRoles(($role ?: []));

            if ($originalRole?->name !== $user->roles()->first()?->name) {
                (new SlackMessage())
                    ->to(config('app.slack_support'))
                    ->content(auth()->user()->username." changed role for {$user->username}!".PHP_EOL.'From _'.($originalRole ? $originalRole->name : 'None')."_ to _{$role}_")
                    ->success();
            }
        }

        $user->fill($data);
        $user->save();

        return $user;
    }

    public function updateGeneral(User $user, UserGeneralUpdateRequest $request): User
    {
        $data = [
            'features' => [
                'tips' => $request->boolean('features.tips'),
//                'demo' => $request->boolean('features.demo'),
            ],
        ];

        $role = $request->input('role');

        if ($role && auth()->user()->hasRole(Roles::SUPER_ADMINISTRATOR)) {
            $user->syncRoles(request()->input('role'));
        }

        $user->fill($data);
        $user->save();

        return $user;
    }

    public function updatePayouts(User $user, UserPayoutsUpdateRequest $request): User
    {
        /*
                $data = [
                ];

                $role = $request->input('role');

                if ($role && auth()->user()->hasRole(Roles::SUPER_ADMINISTRATOR)) {
                    $user->syncRoles($request->input('role'));
                }

                $user->fill($data);
                $user->save();
        */

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

            Bus::chain([
                new DeleteMediaMetadata('avatars/'.$avatar, 'public'),
                (new UploadAvatar((string) $user->_id, 'avatars/'.$avatar))->delay(10),
            ])
            ->onQueue(config('app.server_specific_queue'))
            ->dispatch();
            DeleteAvatar::dispatchIf(! str_contains($oldFileName, 'default_'), basename($oldFileName))->delay(100);
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

        UserStatusChangedEvent::dispatchIf($noteType, $this->getUser(), $user, $noteType);

        return $this->userRepository->update(['status' => $userStatus], $id);
    }

    /**
     * @param mixed $userId
     * @param int $userVisibility
     * @return bool
     */
    public function updateVisibility(mixed $userId, int $userVisibility): bool
    {
        $user = $this->find($userId);

        $noteType = match ($userVisibility) {
            UserVisibility::PUBLIC->value => NoteType::PUBLIC->value,
            UserVisibility::PRIVATE->value => NoteType::PRIVATE->value,
            UserVisibility::INVISIBLE_BY_ADMIN->value => NoteType::INVISIBLE_BY_ADMIN->value,
            default => null
        };

        UserVisibilityChangedEvent::dispatchIf($noteType, $this->getUser(), $user, $noteType);

        return $this->userRepository->update(['visibility' => $userVisibility], $userId);
    }

    /**
     * @param mixed $userId
     * @param array $userSettings
     * @return bool
     */
    public function updatePayoutSettings(mixed $userId, array $payoutSettings): bool
    {
        $user = $this->find($userId);

        $success = true;
        $noteType = null;

        foreach ($payoutSettings as $key => $value) {
            if ($success) {
                $setting = $user['setting'];
                $setting['payout'][$key] = (bool) $value;
                $success = $this->userRepository->update(['setting' => $setting], $userId);

                switch($key) {
                    case 'ban_payout':
                        $noteType = ($value ? NoteType::SET_BAN_PAYOUT->value : NoteType::UNSET_BAN_PAYOUT->value);
                        break;

                    case 'auto_ban_payout':
                        $noteType = ($value ? NoteType::SET_AUTO_BAN_PAYOUT->value : NoteType::UNSET_AUTO_BAN_PAYOUT->value);
                        break;
                }
            }
        }

        UserSettingChangedEvent::dispatchIf($noteType, $this->getUser(), $user, $noteType);

        return $success;
    }

    /**
     * @param mixed $userId
     * @param string $password
     * @return bool
     */
    public function setPassword(mixed $userId, string $password): bool
    {
        $user = $this->find($userId);
        $noteType = NoteType::SET_PASSWORD->value;

        $success = $this->userRepository->update(['password_hash' => Hash::make($password)], $userId);

        UserPasswordChangedEvent::dispatchIf($success, $this->getUser(), $user, $noteType);

        return $success;
    }
}
