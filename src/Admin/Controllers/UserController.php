<?php

namespace Aparlay\Core\Admin\Controllers;

use Aparlay\Core\Admin\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userService;

    public function __construct(
        UserService $userService
    ) {
        $this->userService = $userService;
    }

    /**
     * @throws \ErrorException
     */
    public function index()
    {
        $users = $this->userService->getFilteredUsers();
        $userStatuses = $this->userService->getUserStatuses();

        return view('default_view::admin.pages.user.index', compact('users', 'userStatuses'));
    }

    public function view($id)
    {
        $user = $this->userService->find($id);

        return view('default_view::admin.pages.user.view', compact('user'));
    }
}
