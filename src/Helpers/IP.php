<?php

namespace Aparlay\Core\Helpers;

class IP
{
    public static function trueAddress(): string
    {
        return request()->header('True-Client-IP') ?? request()->header('CF-Connecting-IP') ?? request()->ip();
    }
}
