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

    public function delete(Note $note)
    {
    }
}
