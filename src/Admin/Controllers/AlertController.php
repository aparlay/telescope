<?php

namespace Aparlay\Core\Admin\Controllers;

use Aparlay\Core\Admin\Requests\AlertRequest;
use Aparlay\Core\Admin\Services\AlertService;

class AlertController extends Controller
{
    protected $alertService;

    public function __construct(AlertService $alertService)
    {
        $this->alertService = $alertService;
    }

    /**
     * @throws \ErrorException
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
     * @return mixed
     */
    public function store(AlertRequest $request)
    {
        if ($this->alertService->create()) {
            return back()->with('success', 'Alert added successfully.');
        }

        return back()->with('error', 'Add alert failed.');
    }
}
