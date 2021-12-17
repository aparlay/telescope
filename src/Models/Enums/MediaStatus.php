<?php

namespace Aparlay\Core\Models\Enums;

enum MediaStatus: int implements Enum
{
    case QUEUED = 0;
    case UPLOADED = 1;
    case IN_PROGRESS = 2;
    case COMPLETED = 3;
    case FAILED = 4;
    case CONFIRMED = 5;
    case DENIED = 6;
    case IN_REVIEW = 7;
    case ADMIN_DELETED = 9;
    case USER_DELETED = 10;

    public function label(): string
    {
        return match($this)
        {
            self::QUEUED => __('queued'),
            self::UPLOADED => __('uploaded'),
            self::IN_PROGRESS => __('in progress'),
            self::COMPLETED => __('completed'),
            self::FAILED => __('failed'),
            self::CONFIRMED => __('confirmed'),
            self::DENIED => __('denied'),
            self::IN_REVIEW => __('in review'),
            self::ADMIN_DELETED => __('admin deleted'),
            self::USER_DELETED => __('user deleted'),
        };
    }

    public function badgeColor(): string
    {
        return match($this)
        {
            self::QUEUED, self::UPLOADED => 'info',
            self::IN_PROGRESS, self::IN_REVIEW => 'indigo',
            self::COMPLETED => 'default',
            self::FAILED, self::ADMIN_DELETED, self::USER_DELETED => 'danger',
            self::CONFIRMED => 'success',
            self::DENIED => 'warning',
        };
    }
}
