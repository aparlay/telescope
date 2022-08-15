<?php

namespace Aparlay\Core\Admin\Models;

use Aparlay\Core\Models\Analytic as AnalyticBase;
use OwenIt\Auditing\Contracts\Auditable;

class Analytic extends AnalyticBase implements Auditable
{
    use \OwenIt\Auditing\Auditable;
}
