<?php

namespace Aparlay\Core\Admin\Controllers;

use Aparlay\Core\Admin\Models\User;
use Aparlay\Core\Admin\Requests\MediaUploadRequest;
use Aparlay\Core\Admin\Requests\UserStatusRequest;
use Aparlay\Core\Admin\Requests\UserUpdateRequest;
use Aparlay\Core\Admin\Resources\UserResource;
use Aparlay\Core\Admin\Services\MediaService;
use Aparlay\Core\Admin\Services\UploadService;
use Aparlay\Core\Admin\Services\UserService;
use Aparlay\Core\Models\Enums\UserStatus;
use ErrorException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cookie;
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

    public function moderationQueue()
    {
        $user = $this->userService->firstPending();
        if ($user) {
            return redirect()->route('core.admin.user.view', ['user' => $user->_id])->with([]);
        }

        return redirect()->route('core.admin.user.index')->with([
            'warning' => "Moderation queue is empty"
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

    /**
     * @return UserResource
     */
    public function indexAjax()
    {
        return new UserResource($this->userService->getFilteredUsers());
    }

    public function view(User $user)
    {
        $user = $this->userService->find($user->_id);
        $moderationQueueNotEmpty = $this->userService->isModerationQueueNotEmpty();
        $roles = Role::where('guard_name', 'admin')->get();

        return view('default_view::admin.pages.user.edit', compact('user', 'roles', 'moderationQueueNotEmpty'));
    }

    /**
     * @param User $user
     * @param UserUpdateRequest $request
     * @return RedirectResponse
     */
    public function update(User $user, UserUpdateRequest $request): RedirectResponse
    {
        $this->userService->update($user);

        return back()->with('success', 'User updated successfully.');
    }

    /**
     * @param User $user
     * @param UserStatusRequest $request
     * @return RedirectResponse
     */
    public function updateStatus(User $user, UserStatusRequest $request): RedirectResponse
    {
        $status = request()->input('status');
        if ($this->userService->updateStatus($user->_id)) {
            if ($status == UserStatus::ACTIVE->value) {
                return back()->with('success', 'User Reactivated successfully.');
            }

            return back()->with('success', 'User '.ucfirst(User::getStatuses()[$status]).' successfully.');
        }

        return back()->with('error', 'Update status failed.');
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
