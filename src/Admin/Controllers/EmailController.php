<?php

namespace Aparlay\Core\Admin\Controllers;

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
