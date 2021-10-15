<?php

namespace Aparlay\Core\Admin\Models;

use Aparlay\Core\Models\Media as MediaBase;

class Media extends MediaBase
{
    /**
     * @return string
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            self::STATUS_QUEUED => ['color' => 'secondary', 'text' => 'Queued'],
            self::STATUS_UPLOADED => ['color' => 'secondary', 'text' => 'Uploaded'],
            self::STATUS_IN_PROGRESS => ['color' => 'secondary', 'text' => 'In-Progress'],
            self::STATUS_COMPLETED => ['color' => 'warning', 'text' => 'Completed'],
            self::STATUS_FAILED => ['color' => 'danger', 'text' => 'Failed'],
            self::STATUS_CONFIRMED => ['color' => 'indigo', 'text' => 'Confirmed'],
            self::STATUS_DENIED => ['color' => 'danger', 'text' => 'Denied'],
            self::STATUS_IN_REVIEW => ['color' => 'info', 'text' => 'Under Review'],
            self::STATUS_ADMIN_DELETED => ['color' => 'danger', 'text' => 'Deleted By Admin'],
            self::STATUS_USER_DELETED => ['color' => 'danger', 'text' => 'Delete'],
        ];

        return $colors[$this->status];
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
}
