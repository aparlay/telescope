<?php

namespace Aparlay\Core\Admin\Models;

use Aparlay\Core\Admin\Models\Scopes\MediaScope;
use Aparlay\Core\Models\Media as MediaBase;

class Media extends MediaBase
{
    use MediaScope;

    /**
     * @return string
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            self::STATUS_QUEUED => 'secondary',
            self::STATUS_UPLOADED => 'secondary',
            self::STATUS_IN_PROGRESS => 'secondary',
            self::STATUS_COMPLETED => 'warning',
            self::STATUS_FAILED => 'danger',
            self::STATUS_CONFIRMED => 'indigo',
            self::STATUS_DENIED => 'danger',
            self::STATUS_IN_REVIEW => 'info',
            self::STATUS_ADMIN_DELETED => 'danger',
            self::STATUS_USER_DELETED => 'danger'
        ];

        return $colors[$this->status];
    }

    /**
     * @return string
     */
    public function getStatusNameAttribute()
    {
        return $this->getStatuses()[$this->status];
    }
}
