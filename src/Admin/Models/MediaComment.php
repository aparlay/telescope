<?php

namespace Aparlay\Core\Admin\Models;

use Aparlay\Core\Models\MediaComment as MediaCommentBase;
use OwenIt\Auditing\Contracts\Auditable;

class MediaComment extends MediaCommentBase implements Auditable
{
    use \OwenIt\Auditing\Auditable;
}
