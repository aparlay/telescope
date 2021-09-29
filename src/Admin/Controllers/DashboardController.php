<?php

namespace Aparlay\Core\Admin\Controllers;

use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    public function dashboard()
    {
        return view('default_view::admin.pages.dashboard.index');
    }
}
