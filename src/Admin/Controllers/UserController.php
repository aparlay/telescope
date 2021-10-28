<?php

namespace Aparlay\Core\Admin\Controllers;

use Aparlay\Core\Admin\Models\User;
use Aparlay\Core\Admin\Requests\UserRequest;
use Aparlay\Core\Admin\Resources\UserResource;
use Aparlay\Core\Admin\Services\UserService;
use ErrorException;
use Illuminate\Http\RedirectResponse;
use Maklad\Permission\Models\Role;

class UserController extends Controller
{
    protected $userService;

    public function __construct(
        UserService $userService
    ) {
        $this->userService = $userService;
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
        $roles = Role::all();

        return view('default_view::admin.pages.user.edit', compact('user', 'roles'));
    }

    /**
     * @param User $user
     * @param UserRequest $request
     * @return RedirectResponse
     */
    public function update(User $user, UserRequest $request): RedirectResponse
    {
        $this->userService->update($user);

        return back()->with('success', 'User updated successfully.');
    }

    /**
     * @param User $user
     * @param $status
     * @return RedirectResponse
     */
    public function updateStatus(User $user, UserRequest $request): RedirectResponse
    {
        $status = request()->input('status');
        if ($this->userService->updateStatus($user->_id)) {
            if ($status == User::STATUS_ACTIVE) {
                return back()->with('success', 'User Reactivated successfully.');
            }

            return back()->with('success', 'User '.ucfirst(User::getStatuses()[$status]).' successfully.');
        }

        return back()->with('error', 'Update status failed.');
    }
}
