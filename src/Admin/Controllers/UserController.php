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

    public function view($id)
    {
        $user = $this->userService->find($id);
        $roles = Role::all();

        return view('default_view::admin.pages.user.edit', compact('user', 'roles'));
    }

    /**
     * @param $id
     * @param UserRequest $request
     * @return RedirectResponse
     */
    public function update(User $user, UserRequest $request): RedirectResponse
    {
        $this->userService->update($user);

        return back()->with('success', 'User updated successfully.');
    }
}
