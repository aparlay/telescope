<?php

namespace Aparlay\Core\Admin\Services;

use Aparlay\Core\Admin\Models\Analytic;
use Aparlay\Core\Admin\Repositories\AnalyticRepository;
use Illuminate\Support\Collection;

class AnalyticService
{
    protected AnalyticRepository $analyticRepository;

    public function __construct()
    {
        $this->analyticRepository = new AnalyticRepository(new Analytic());
    }

    public function getAnalytics($fromDate = null, $toDate = null): Collection
    {
        $fromDate = $fromDate ?? date('Y-m-d', strtotime('-7 days'));
        $toDate   = $toDate   ?? date('Y-m-d');

        return $this->analyticRepository->getAnalytics($fromDate, $toDate);
    }
}
