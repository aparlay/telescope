<?php

namespace Aparlay\Core\Commands;

use Aparlay\Core\Microservices\ws\WsChannel;
use Aparlay\Core\Microservices\ws\WsDispatcherFactory;
use Carbon\Carbon;
use Co;
use Flow\FileOpenException;
use Flow\Uploader;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use JWTAuth;
use Swoole\Coroutine\Http\Client;
use Swoole\Runtime;
use Swoole\Timer;
use Swoole\WebSocket\Frame;
use Tymon\JWTAuth\Claims\Audience;
use Tymon\JWTAuth\Claims\Collection;
use Tymon\JWTAuth\Claims\Custom;
use Tymon\JWTAuth\Claims\Expiration;
use Tymon\JWTAuth\Claims\IssuedAt;
use Tymon\JWTAuth\Claims\Issuer;
use Tymon\JWTAuth\Claims\JwtId;
use Tymon\JWTAuth\Claims\NotBefore;
use Tymon\JWTAuth\Claims\Subject;
use Tymon\JWTAuth\Payload;
use Tymon\JWTAuth\Validators\PayloadValidator;
use yii\console\ExitCode;

class CleanupCommand extends Command
{
    public $signature = 'core:ws';

    public $description = 'Aparlay Ws Client';

    public function handle()
    {
        try {
            Uploader::pruneChunks(config('app.avatar.upload_directory'));
        } catch (FileOpenException $e) {
            return ExitCode::TEMPFAIL;
        }
    }
}
