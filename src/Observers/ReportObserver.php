<?php

namespace Aparlay\Core\Observers;

use Aparlay\Core\Api\V1\Notifications\ReportSent;
use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Models\Report;
use MongoDB\BSON\ObjectId;

class ReportObserver
{
    /**
     * Handle the Report "creating" event.
     *
     * @param  Report  $report
     * @return void
     */
    public function creating(Report $report)
    {
    }

    /**
     * Handle the Report "created" event.
     *
     * @param  Report  $report
     * @return void
     */
    public function created(Report $report)
    {
        $report->notify(new ReportSent());
    }
}
