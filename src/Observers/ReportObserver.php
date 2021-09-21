<?php

namespace Aparlay\Core\Observers;

use Aparlay\Core\Models\Report;
use Aparlay\Core\Api\V1\Notifications\ReportSent;


class ReportObserver
{
    
    /**
     * Handle the Report "created" event.
     *
     * @param  Report  $report
     * @return void
     */
    public function created(Report $report)
    {  
       $report->notify( new ReportSent());
    }

}