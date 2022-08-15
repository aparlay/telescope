<?php

namespace Aparlay\Core\Admin\Models;

use Aparlay\Core\Models\Alert as BaseAlert;
use OwenIt\Auditing\Contracts\Auditable;

class Alert extends BaseAlert implements Auditable
{
    use \OwenIt\Auditing\Auditable;
}
