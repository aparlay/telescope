<?php

namespace Aparlay\Core\Commands;

use Aparlay\Core\Microservices\ws\WsChannel;
use Aparlay\Core\Microservices\ws\WsDispatcherFactory;
use Carbon\Carbon;
use Co;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use PHPOpenSourceSaver\JWTAuth\Claims\Audience;
use PHPOpenSourceSaver\JWTAuth\Claims\Collection;
use PHPOpenSourceSaver\JWTAuth\Claims\Custom;
use PHPOpenSourceSaver\JWTAuth\Claims\Expiration;
use PHPOpenSourceSaver\JWTAuth\Claims\IssuedAt;
use PHPOpenSourceSaver\JWTAuth\Claims\Issuer;
use PHPOpenSourceSaver\JWTAuth\Claims\JwtId;
use PHPOpenSourceSaver\JWTAuth\Claims\NotBefore;
use PHPOpenSourceSaver\JWTAuth\Claims\Subject;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Payload;
use PHPOpenSourceSaver\JWTAuth\Validators\PayloadValidator;
use Swoole\Coroutine\Http\Client;
use Swoole\Runtime;
use Swoole\WebSocket\Frame;

class WsCommand extends Command
{
    public $signature = 'core:ws';

    public $description = 'Aparlay Ws Client';

    public function handle()
    {
        $requiredClaims = [
            new Audience('https://'.gethostname()),
            new Issuer(config('app.jwt.sig.issuer')),
            new IssuedAt(Carbon::now('UTC')),
            new Expiration(Carbon::now('UTC')->addYear(1)),
            new NotBefore(Carbon::now('UTC')),
            new JwtId(Str::random()),
            new Subject('0'),
            new Custom('role', 'system'),
            new Custom('device_id', '0'),
        ];

        $claims = new Collection($requiredClaims);
        $payload = new Payload($claims, new PayloadValidator());
        $tokenInstance = JWTAuth::encode($payload);
        $token = $tokenInstance->get();
        Runtime::enableCoroutine(SWOOLE_HOOK_ALL);
        ini_set('default_socket_timeout', -1);
        \Co\run(function () use ($token) {
            $client = new Client(config('app.websocket.host'), config('app.websocket.port'));
            $client->setHeaders([
                'Authorization' => 'Bearer '.$token,
                'Device_id' => '0',
                'User_id' => '0',
            ]);
            $ret = $client->upgrade('/');
            if ($ret) {
                $this->info('Connection established successfully!');
                go(function () use ($client) {
                    $redis = Redis::connection();
                    $redis->setOption(\Redis::OPT_READ_TIMEOUT, -1);
                    $redis->subscribe([WsChannel::REDIS_CHANNEL], function ($message) use ($client) {
                        $this->info('New broadcasting message arrived!');
                        $this->info($message);
                        $client->push($message);
                    });
                });

                while (true) {
                    if (($frame = $client->recv()) instanceof Frame) {
                        $this->info('New WS message arrived!');
                        $this->info($frame->data);
                        $data = json_decode($frame->data, true, 512, JSON_THROW_ON_ERROR);
                        if (isset($data['event'], $data['properties'])) {
                            $properties = $data['properties'];
                            $properties['deviceId'] = $data['deviceId'] ?? null;
                            $properties['userId'] = $data['userId'] ?? null;
                            $properties['anonymousId'] = $data['anonymousId'] ?? null;

                            $dispatcher = WsDispatcherFactory::construct($data['event'], $properties);
                            $dispatcher->execute();
                        }
                    }
                    Co::sleep(0.1);
                }
            }

            $client->close();
        });

        $this->error('Cannot connect to the server!');
    }
}
