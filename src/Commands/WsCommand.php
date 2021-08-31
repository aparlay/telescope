<?php

namespace Aparlay\Core\Commands;

use Aparlay\Core\Microservices\ws\WsDispatcherFactory;
use co;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use Swoole\Coroutine\Http\Client;
use Swoole\Runtime;
use Swoole\WebSocket\Frame;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;

class WsCommand extends Command
{
    public $signature = 'core:ws';

    public $description = 'Aparlay Ws Client';

    public function handle()
    {
        $payload['sub'] = '0';
        $payload['role'] = 'system';
        $payload['device_id'] = '0';
        $payLoad = JWTFactory::make($payload);
        $token = JWTAuth::encode($payLoad)->get();
        Runtime::enableCoroutine(SWOOLE_HOOK_ALL);
        \Co\run(function () use ($token) {
            $client = new Client(config('app.websocket.host'), config('app.websocket.port'));
            $client->setHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Device_id' => '0',
                'User_id' => '0',
            ]);
            $ret = $client->upgrade('/');
            if ($ret) {
                $this->info('Connection established successfully!');
                go(function() use ($client) {
                    Redis::subscribe(['test-channel'], function ($message) use ($client) {
                        $this->info('New broadcasting message arrived!');
                        $this->info($message);
                        $client->push($message);
                    });
                });

                while (true) {
                    if (($frame = $client->recv()) instanceof Frame) {
                        $this->info('New WS message arrived!');
                        $this->info($frame->data);
                        $data = json_decode($frame->data, true);
                        if (isset($data['event'], $data['properties'])) {
                            $properties = $data['properties'];
                            $properties['deviceId'] = $data['deviceId'] ?? null;
                            $properties['userId'] = $data['userId'] ?? null;
                            $properties['anonymousId'] = $data['anonymousId'] ?? null;
                            $dispatcher = WsDispatcherFactory::construct($data['event'], $properties);
                            $dispatcher->execute();
                        }
                    }
                    co::sleep(0.1);
                }
            }

            $client->close();
        });

        $this->comment('All done');
    }
}
