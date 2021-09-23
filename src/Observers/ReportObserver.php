<?php

namespace Aparlay\Core\Observers;

use Aparlay\Core\Api\V1\Notifications\ReportSent;
use Aparlay\Core\Models\Report;

class ReportObserver extends BaseModelObserver
{
    /**
     * Handle the Report "created" event.
     *
     * @param  Report  $report
     * @return void
     */
    public function created(Report $report): void
    {
        $report->notify(new ReportSent());
    }
}
