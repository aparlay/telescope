<?php

namespace Aparlay\Core\Admin\Controllers;

use Aparlay\Core\Admin\Resources\EmailResource;
use Aparlay\Core\Admin\Services\EmailService;

/**
 * Class EmailController.
 */
class EmailController extends Controller
{
    public function index()
    {
        return view('default_view::admin.pages.email.index');
    }
}
