<?php

namespace Aparlay\Core\Admin\Models;

use Aparlay\Core\Models\Email as EmailBase;
use Aparlay\Core\Models\Scopes\BaseScope;
use Aparlay\Core\Models\Scopes\DateScope;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * Class Email.
 */
class Email extends EmailBase implements Auditable
{
    use BaseScope;
    use DateScope;
    use \OwenIt\Auditing\Auditable;

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
