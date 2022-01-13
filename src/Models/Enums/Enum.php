<?php

namespace Aparlay\Core\Models\Enums;

interface Enum
{
    public function label(): string;

    public function badgeColor(): string;
}
