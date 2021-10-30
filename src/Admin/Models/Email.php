<?php

namespace Aparlay\Core\Admin\Models;

use Aparlay\Core\Models\Email as EmailBase;
use Aparlay\Core\Models\Scopes\EmailScope;

/**
 * Class Email
 * @package Aparlay\Core\Admin\Models
 */
class Email extends EmailBase
{
    use EmailScope;

    /**
     * @return string
     */
    public function getStatusNameAttribute()
    {
        return isset($this->getStatuses()[$this->attributes['attributes']['status']])
            ? $this->getStatuses()[$this->attributes['attributes']['status']]
            : '';
    }

    /**
     * @return string
     */
    public function getTypeNameAttribute()
    {
        return $this->attributes['attributes']['type'] == 1
            ? $this->getTypes()[0]
            : '';
    }
}
