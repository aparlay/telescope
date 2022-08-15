<?php

namespace Aparlay\Core\Admin\Models;

use Aparlay\Core\Models\Note as NoteAlert;
use OwenIt\Auditing\Contracts\Auditable;

class Note extends NoteAlert implements Auditable
{
    use \OwenIt\Auditing\Auditable;
}
