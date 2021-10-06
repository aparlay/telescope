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

        return view('default_view::admin.pages.user.index', compact('users'));
    }
}
