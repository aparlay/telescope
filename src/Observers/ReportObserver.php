<?php

namespace Aparlay\Core\Observers;

use Aparlay\Core\Api\V1\Notifications\ReportSent;
use Aparlay\Core\Models\Report;

class ReportObserver extends BaseModelObserver
{
    /**
     * Handle the Report "created" event.
     *
     * @param Report $model
     */
    public function created($model): void
    {
        if (config('app.env') !== 'testing') {
            $model->notify(new ReportSent($model));
        }
    }
}
