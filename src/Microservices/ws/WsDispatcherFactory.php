<?php

namespace Aparlay\Core\Microservices\ws;

use Aparlay\Core\Microservices\ws\media\Watch;
use InvalidArgumentException;

class WsDispatcherFactory
{
    public const CLASS_MAP = [
        'media.watch' => Watch::class,
    ];

    public static function construct(string $event, array $properties): WsEventDispatcher
    {
        if (!array_key_exists($event, self::CLASS_MAP)) {
            throw new InvalidArgumentException('Required events does not supported!');
        }

        $class = self::CLASS_MAP[$event];

        return new $class($properties);
    }
}
