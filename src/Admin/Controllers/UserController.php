<?php

namespace Aparlay\Core\Admin\Controllers;

use Aparlay\Core\Admin\Resources\UserResource;
use Aparlay\Core\Admin\Services\UserService;
use ErrorException;
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
     * @throws ErrorException
     */
    public function index()
    {
        $userStatuses = $this->userService->getUserStatuses();
        $userVisibilities = $this->userService->getVisibilities();

        return view('default_view::admin.pages.user.index', compact('userStatuses', 'userVisibilities'));
    }

    public function indexAjax()
    {
        return new UserResource($this->userService->getFilteredUsers());
    }

    public function view($id)
    {
        $user = $this->userService->find($id);

        return view('default_view::admin.pages.user.view', compact('user'));
    }
}
