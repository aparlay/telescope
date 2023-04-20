<?php

namespace Aparlay\Core\Admin\Controllers;

use Aparlay\Core\Admin\Models\User;
use Aparlay\Core\Admin\Requests\AlertRequest;
use Aparlay\Core\Admin\Services\AlertService;
use ErrorException;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    protected $alertService;

    public function __construct(AlertService $alertService)
    {
        $this->alertService = $alertService;
    }

    /**
     * @throws ErrorException
     */
    public function index()
    {
    }

    public function view($id)
    {
    }

    /**
     * Creates a new Alert model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function store(AlertRequest $request)
    {
        $this->alertService->setUser(auth()->user());
        if ($this->alertService->create()) {
            return back()->with('success', 'Alert added successfully.');
        }

        return back()->with('error', 'Add alert failed.');
    }

    /**
     * Creates a new Alert model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function storeForUSer(Request $request, User $user)
    {
        $this->alertService->setUser(auth()->user());

        if ($this->alertService->forUser($user, $request->input('reason'))) {
            return back()->with('success', 'Alert added successfully.');
        }

        return back()->with('error', 'Add alert failed.');
    }
}
