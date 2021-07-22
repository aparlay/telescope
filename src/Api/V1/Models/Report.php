<?php

namespace Aparlay\Core\Api\V1\Models;

use Aparlay\Core\Models\Report as ReportBase;
use Illuminate\Notifications\Notifiable;

class Report extends ReportBase
{
    use Notifiable;
}
