<?php

namespace Aparlay\Core;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Aparlay\Core\Core
 */
class CoreFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'Core';
    }
}
