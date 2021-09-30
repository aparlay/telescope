<?php

namespace Aparlay\Core\Admin\Controllers;

use Aparlay\Core\Admin\Services\AnalyticService;

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

        return view('default_view::admin.pages.dashboard.index')->with(['data_analytics' => $data_analytics]);
    }
}
