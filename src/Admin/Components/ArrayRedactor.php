<?php

namespace Aparlay\Core\Admin\Components;

class ArrayRedactor implements \OwenIt\Auditing\Contracts\AttributeRedactor
{

    /**
     * @inheritDoc
     */
    public static function redact($value): string
    {
        return is_array($value) ? json_encode($value) : $value;
    }
}
