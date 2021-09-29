<?php

namespace Aparlay\Core\Admin\Repositories\Analytic;

use Aparlay\Core\Admin\Models\Analytic;
use Illuminate\Support\Collection;

class AnalyticRepository implements AnalyticRepositoryInterface
{
    protected $model;

    public function __construct(Analytic $analytic)
    {
        $this->model = $analytic;
    }

    public function getUserAnalytics(): Collection
    {
        return $this->model->latest()->take(20)->get()->sortBy('date');
    }
}
