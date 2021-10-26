<?php

namespace Aparlay\Core\Admin\Controllers;

use Aparlay\Core\Admin\Services\AlertService;

class AlertController
{
    protected $alertService;

    public function __construct(
        AlertService $alertService
    ) {
        $this->alertService = $alertService;
    }

    public function store()
    {
        if($this->alertService->store()) {
            return back()->with('success', 'Alert added successfully.');
        }

        return back()->with('error', 'Add alert failed.');
    }
}
