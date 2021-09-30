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

    public function getAnalytics(): Collection
    {
        return $this->analyticRepository->getAnalytics();
    }
}
