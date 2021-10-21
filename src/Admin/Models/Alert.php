<?php

namespace Aparlay\Core\Admin\Models;

use Aparlay\Core\Admin\Models\Scopes\AlertScope;
use Aparlay\Core\Models\Alert as MediaBase;

class Alert extends MediaBase
{
    use AlertScope;
}
