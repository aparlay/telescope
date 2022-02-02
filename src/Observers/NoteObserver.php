<?php

namespace Aparlay\Core\Observers;

use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Models\Enums\NoteType;
use Aparlay\Core\Models\Follow;
use Aparlay\Core\Models\Note;
use Illuminate\Support\Facades\Redis;
use MongoDB\BSON\ObjectId;

class NoteObserver extends BaseModelObserver
{
}
