<?php

namespace Aparlay\Core\Admin\Models;

use Aparlay\Core\Models\Enums\MediaStatus;
use Aparlay\Core\Models\Media as MediaBase;
use Aparlay\Core\Models\Scopes\MediaScope;

class Media extends MediaBase
{
    use MediaScope;

    /**
     * @return string
     */
    public function getStatusColorAttribute()
    {
        return MediaStatus::from($this->status)->badgeColor();
    }

    /**
     * @return string
     */
    public function getStatusNameAttribute()
    {
        return MediaStatus::from($this->status)->label();
    }

    public static function getSkinScores()
    {
        return range(0, 10);
    }

    public static function getAwesomenessScores()
    {
        return range(0, 10);
    }
}
