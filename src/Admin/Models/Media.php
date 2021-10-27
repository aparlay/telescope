<?php

namespace Aparlay\Core\Admin\Models;

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
            self::STATUS_USER_DELETED => 'danger',
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

    public function getSkinScores()
    {
        return [
            0 => '0',
            1 => '1',
            2 => '2',
            3 => '3',
            4 => '4',
            5 => '5',
            6 => '6',
            7 => '7',
            8 => '8',
            9 => '9',
            10 => '10',
        ];
    }

    public function getAwesomenessScores()
    {
        return [
            0 => '0',
            1 => '1',
            2 => '2',
            3 => '3',
            4 => '4',
            5 => '5',
            6 => '6',
            7 => '7',
            8 => '8',
            9 => '9',
            10 => '10',
        ];
    }

    public function url($type = 'video')
    {
        switch ($type) {
            case 'video':
                return config('app.cdn.videos');
            case 'cover':
                return config('app.cdn.covers');
        }
    }
}
