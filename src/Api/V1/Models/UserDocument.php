<?php

namespace Aparlay\Core\Api\V1\Models;

use Aparlay\Core\Models\UserDocument as UserDocumentBase;
use Jenssegers\Mongodb\Relations\MorphMany;

class UserDocument extends UserDocumentBase
{
    public function alertObjs(): \Illuminate\Database\Eloquent\Relations\MorphMany|MorphMany
    {
        return $this->morphMany(Alert::class, 'entity.');
    }
}
