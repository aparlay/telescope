<?php

namespace Aparlay\Core\Admin\Models;

use Aparlay\Core\Models\Enums\MediaStatus;
use Aparlay\Core\Models\Media as MediaBase;
use Aparlay\Core\Models\Scopes\MediaScope;
use OwenIt\Auditing\Contracts\Auditable;

class Media extends MediaBase implements Auditable
{
    use MediaScope;
    use \Aparlay\Core\Admin\Models\Auditable;

    /**
     * Should the audit be strict?
     *
     * @var bool
     */
    protected $auditStrict = true;

    /**
     * Attributes to exclude from the Audit.
     *
     * @var array
     */
    protected $auditExclude = [
        'updated_by',
    ];

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
        return range(1, 10);
    }

    public static function getAwesomenessScores()
    {
        return range(1, 10);
    }

    public static function getBeautyScores()
    {
        return range(1, 10);
    }
}
