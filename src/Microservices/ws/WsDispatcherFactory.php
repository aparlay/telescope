<?php


namespace Aparlay\Core\Microservices\ws;


use Aparlay\Core\Microservices\ws\media\Watch;

class WsDispatcherFactory
{

    public const CLASS_MAP = [
        'media.watch' => Watch::class,
    ];

    /**
     * @param string $event
     * @param array $properties
     * @return WsEventDispatcher
     */
    public static function construct(string $event, array $properties): WsEventDispatcher
    {
        if (!array_key_exists($event, self::CLASS_MAP)) {
            throw new \InvalidArgumentException('Required events does not supported!');
        }

        $class = self::CLASS_MAP[$event];

        return new $class($properties);
    }
}
