<?php
namespace Aparlay\Core\Models\Enums;
enum UserVerificationStatus: int implements Enum
{
    case PENDING = 1;
    case VERIFIED = 2;
    case REJECTED = -1;
    public function label(): string
    { 
        return match ($this) {
            self::PENDING => __('pending'),
            self::VERIFIED => __('verified'),
            self::REJECTED => __('rejected'),
        };
    }
    public function badgeColor(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::VERIFIED => 'success',
            self::REJECTED => 'danger',
        };
    }
}