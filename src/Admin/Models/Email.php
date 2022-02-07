<?php

namespace Aparlay\Core\Admin\Models;

use Aparlay\Core\Models\Email as EmailBase;
use Aparlay\Core\Models\Scopes\BaseScope;
use Aparlay\Core\Models\Scopes\DateScope;

/**
 * Class Email.
 */
class Email extends EmailBase
{
    use BaseScope;
    use DateScope;

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
