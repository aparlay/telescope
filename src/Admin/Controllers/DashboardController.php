<?php

namespace Aparlay\Core\Admin\Controllers;

use Aparlay\Core\Admin\Services\AnalyticService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $analyticService;

    public function __construct(
        AnalyticService $analyticService
    ) {
        $this->analyticService = $analyticService;
    }

    public function dashboard()
    {
        $data_analytics = $this->analyticService->getAnalytics();
        $breadcrumbs    = ['title' => 'Dashboard'];

        return view('default_view::admin.pages.dashboard.index')->with(['data_analytics' => $data_analytics, 'breadcrumbs' => $breadcrumbs]);
    }

    public function indexAjax(Request $request)
    {
        $data_analytics = $this->analyticService->getAnalytics($request->get('fromDate'), $request->get('toDate'));

        return $data_analytics;
    }
}
