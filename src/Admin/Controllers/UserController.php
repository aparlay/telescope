<?php

namespace Aparlay\Core\Admin\Controllers;

use Aparlay\Core\Admin\Models\User;
use Aparlay\Core\Admin\Requests\MediaUploadRequest;
use Aparlay\Core\Admin\Requests\PushNotificationRequest;
use Aparlay\Core\Admin\Requests\UserGeneralUpdateRequest;
use Aparlay\Core\Admin\Requests\UserInfoUpdateRequest;
use Aparlay\Core\Admin\Requests\UserPasswordRequest;
use Aparlay\Core\Admin\Requests\UserPayoutsUpdateRequest;
use Aparlay\Core\Admin\Requests\UserProfileUpdateRequest;
use Aparlay\Core\Admin\Requests\UserSettingsRequest;
use Aparlay\Core\Admin\Requests\UserStatusRequest;
use Aparlay\Core\Admin\Requests\UserVisibilityRequest;
use Aparlay\Core\Admin\Services\MediaService;
use Aparlay\Core\Admin\Services\UploadService;
use Aparlay\Core\Admin\Services\UserService;
use Aparlay\Core\Models\Country;
use Aparlay\Core\Models\Enums\UserStatus;
use ErrorException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Maklad\Permission\Models\Role;

class UserController extends Controller
{
    protected $userService;

    protected $mediaService;

    public function __construct(
        UserService $userService,
        MediaService $mediaService
    ) {
        $this->userService = $userService;
        $this->mediaService = $mediaService;
    }

    /**
     * @param $userId
     * @param $direction
     * @return RedirectResponse
     */
    public function moderationNextOrPrev($userId, $direction)
    {
        $currentUser = auth()->user();

        if ((int) $direction === 1) {
            $user = $this->userService->firstNextPending($currentUser, $userId);
        } else {
            $user = $this->userService->firstPrevPending($currentUser, $userId);
        }

        if ($user) {
            return redirect()->route('core.admin.user.view', ['user' => $user->_id])->with([]);
        }

        return redirect()->route('core.admin.user.index')->with([
            'warning' => 'Moderation queue is empty',
        ]);
    }

    public function moderationQueue()
    {
        $user = $this->userService->firstPending(auth()->user());

        if ($user) {
            return redirect()->route('core.admin.user.view', [
                'user' => $user->_id,
            ])->with([]);
        }

        return redirect()->route('core.admin.user.index')->with([
            'warning' => 'Moderation queue is empty',
        ]);
    }

    public function moderation()
    {
        return view('default_view::admin.pages.user.moderation');
    }

    /**
     * @throws ErrorException
     */
    public function index()
    {
        $userStatuses = $this->userService->getUserStatuses();
        $userVisibilities = $this->userService->getVisibilities();

        return view('default_view::admin.pages.user.index', compact('userStatuses', 'userVisibilities'));
    }

    public function view(User $user)
    {
        $user = $this->userService->find($user->_id);
        $moderationQueueNotEmpty = $this->userService->isModerationQueueNotEmpty();
        $roles = Role::where('guard_name', 'admin')->get();
        $countries = Country::query()->get();

        $hasPrev = $this->userService->hasPrevPending($user->_id);
        $hasNext = $this->userService->hasNextPending($user->_id);

        return view('default_view::admin.pages.user.edit', compact(
            'user',
            'roles',
            'moderationQueueNotEmpty',
            'hasNext',
            'hasPrev',
            'countries'
        ));
    }

    /**
     * @param User $user
     * @param UserProfileUpdateRequest $request
     * @return RedirectResponse
     */
    public function updateProfile(User $user, UserProfileUpdateRequest $request): RedirectResponse
    {
        $this->userService->updateProfile($user, $request);

        return back()->with('success', 'User updated successfully.');
    }

    /**
     * @param User $user
     * @param UserInfoUpdateRequest $request
     * @return RedirectResponse
     */
    public function updateInfo(User $user, UserInfoUpdateRequest $request): RedirectResponse
    {
        $this->userService->updateInfo($user, $request);

        return back()->with('success', 'User updated successfully.');
    }

    /**
     * @param User $user
     * @param UserGeneralUpdateRequest $request
     * @return RedirectResponse
     */
    public function updateGeneral(User $user, UserGeneralUpdateRequest $request): RedirectResponse
    {
        $this->userService->updateGeneral($user, $request);

        return back()->with('success', 'User updated successfully.');
    }

    /**
     * @param User $user
     * @param UserPayoutsUpdateRequest $request
     * @return RedirectResponse
     */
    public function updatePayouts(User $user, UserPayoutsUpdateRequest $request): RedirectResponse
    {
        $this->userService->updatePayouts($user, $request);

        return back()->with('success', 'User updated successfully.');
    }

    /**
     * @param User $user
     * @param UserStatusRequest $request
     * @return RedirectResponse
     */
    public function updateStatus(User $user, UserStatusRequest $request): RedirectResponse
    {
        $this->userService->setUser(auth()->user());

        $status = (int) request()->input('status');

        if ($this->userService->updateStatus($user->_id, $status)) {
            if ($status == UserStatus::ACTIVE->value) {
                return back()->with('success', 'User Reactivated successfully.');
            }

            return back()->with('success', 'User '.ucfirst(User::getStatuses()[$status]).' successfully.');
        }

        return back()->with('danger', 'Update status failed.');
    }

    /**
     * @param User $user
     * @param PushNotificationRequest $request
     * @return RedirectResponse
     */
    public function pushNotifications(User $user, PushNotificationRequest $request): RedirectResponse
    {
        $this->userService->setUser(auth()->user());

        $notification = request()->input('push_notification_type');
        if (class_exists($notification) && $user->routeNotificationForWebPush()->count()) {
            $user->notify(new $notification('Administrator', '0'));

            return back()->with('success', 'Notification '.Str::afterLast($notification, '\\').' sent successfully.');
        }

        return back()->with('danger', 'There is no web push subscribed device for the given user.');
    }

    public function updateVisibility(User $user, UserVisibilityRequest $request): RedirectResponse
    {
        $this->userService->setUser(auth()->user());

        $visibility = (int) $request->input('visibility');

        if ($this->userService->updateVisibility($user->_id, $visibility)) {
            return back()->with('success', 'Set user '.ucfirst(User::getVisibilities()[$visibility]).' successfully.');
        }

        return back()->with('danger', 'Update visibility failed.');
    }

    public function updatePayoutSettings(User $user, UserSettingsRequest $request): RedirectResponse
    {
        $this->userService->setUser(auth()->user());

        $settings = $request->input('setting');

        if ($this->userService->updatePayoutSettings($user->_id, $settings['payout'])) {
            return back()->with('success', 'Set user settings successfully.');
        }

        return back()->with('danger', 'Update settings failed.');
    }

    public function setPassword(User $user, UserPasswordRequest $request): RedirectResponse
    {
        $this->userService->setUser(auth()->user());

        $password = $request->input('password');

        if ($this->userService->setPassword($user->_id, $password)) {
            return back()->with('success', 'Set user password successfully.');
        }

        return back()->with('danger', 'Set password failed.');
    }

    public function upload(MediaUploadRequest $request)
    {
        $this->mediaService->upload();

        return back()->with(['success' => 'New media saved.']);
    }

    public function uploadMedia(Request $request): Response
    {
        $result = UploadService::chunkUpload($request);

        return response($result['data'], $result['code'], []);
    }

    public function loginAsUser(User $user)
    {
        $token = auth('api')->tokenById($user->_id);
        $result = $this->respondWithToken($token);
        $cookie1 = Cookie::make(
            '__Secure_token',
            $result['token'],
            $result['token_expired_at'] / 60
        );
        $cookie2 = Cookie::make(
            '__Secure_refresh_token',
            $result['refresh_token'],
            $result['refresh_token_expired_at'] / 60
        );
        $cookie3 = Cookie::make(
            '__Secure_username',
            $user->username,
            $result['refresh_token_expired_at'] / 60
        );

        return redirect()->away(config('app.frontend_url'))->withCookies([$cookie1, $cookie2, $cookie3]);
    }

    protected function respondWithToken(string $token): array
    {
        return [
            'token' => $token,
            'token_expired_at' => auth('api')->factory()->getTTL() * 60,
            'refresh_token' => $token,
            'refresh_token_expired_at' => auth('api')->factory()->getTTL() * 60,
        ];
    }
}
