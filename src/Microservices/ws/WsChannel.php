<?php

namespace Aparlay\Core\Microservices\ws;

use Illuminate\Support\Facades\Redis;

class WsChannel
{
    public const REDIS_CHANNEL = 'websocket_events';

    /**
     * @param  string  $recipientId
     * @param  string  $event
     * @param  array  $properties
     * @param  array|null  $context
     * @param  string  $type
     * @param  string  $channel
     * @return void
     */
    public static function Push(
        string $recipientId,
        string $event = '',
        array $properties = [],
        array $context = null,
        string $type = 'track',
        string $channel = 'user'
    ): void {
        $message = [
            'channel' => $channel,
            'type' => $type,
            'event' => $event,
            'context' => $context,
            'properties' => $properties,
            'recipientId' => $recipientId,
            'timestamp' => date(DATE_RFC3339_EXTENDED),
        ];

        Redis::publish(self::REDIS_CHANNEL, json_encode($message));
    }
}
