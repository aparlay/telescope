<?php

namespace Aparlay\Core\Admin\Controllers;

use Aparlay\Core\Admin\Repositories\Analytic\AnalyticRepositoryInterface;
use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    private $analytic;

    public function __construct(
        AnalyticRepositoryInterface $analytic
    ){
        $this->analytic = $analytic;
    }

    public function dashboard()
    {
        $data_analytics = $this->analytic->getUserAnalytics();

        return view('default_view::admin.pages.dashboard.index')->with(['data_analytics' => $data_analytics]);
    }
}
