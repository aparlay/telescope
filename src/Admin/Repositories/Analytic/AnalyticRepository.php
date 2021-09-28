<?php

namespace Aparlay\Core\Admin\Repositories\Analytic;

use Aparlay\Core\Models\Analytic;

class AnalyticRepository implements AnalyticRepositoryInterface
{
    protected $model;

    public function __construct(Analytic $analytic) {
        $this->model = $analytic;
    }

    public function getUserAnalytics()
    {
        return $this->model->latest()->take(20)->get()->sortBy('date');
    }
}
