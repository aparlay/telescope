<?php

namespace Aparlay\Core\Admin\Models;

use Aparlay\Core\Models\Email as EmailBase;
use Aparlay\Core\Models\Scopes\AdminBaseScope;
use Aparlay\Core\Models\Scopes\EmailScope;

/**
 * Class Email.
 */
class Email extends EmailBase
{
    use AdminBaseScope;

    /**
     * @return string
     */
    public function getStatusNameAttribute()
    {
        return $this->getStatuses()[$this->status];
    }

    /**
     * @return string
     */
    public function getTypeNameAttribute()
    {
        return $this->getTypes()[$this->type];
    }
}
