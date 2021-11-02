<?php

namespace Aparlay\Core\Admin\Models;

use Aparlay\Core\Models\Email as EmailBase;
use Aparlay\Core\Models\Scopes\EmailScope;

/**
 * Class Email.
 */
class Email extends EmailBase
{
    use EmailScope;

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
