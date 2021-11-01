<?php

namespace Aparlay\Core\Admin\Controllers;

use Aparlay\Core\Admin\Resources\EmailResource;
use Aparlay\Core\Admin\Services\EmailService;
use ErrorException;
use Illuminate\Http\Request;

/**
 * Class EmailController.
 */
class EmailController extends Controller
{
    protected $emailService;

    /**
     * EmailController constructor.
     * @param EmailService $emailService
     */
    public function __construct(
        EmailService $emailService
    ) {
        $this->emailService = $emailService;
    }

    /**
     * @throws \ErrorException
     */
    public function index()
    {
        return view('default_view::admin.pages.email.index')->with([
            'emailStatuses' => $this->emailService->getEmailStatuses(),
            'emailTypes' => $this->emailService->getEmailTypes(),
        ]);
    }

    /**
     * @return EmailResource
     */
    public function indexAjax()
    {
        return new EmailResource($this->emailService->getFilteredEmail());
    }
}
