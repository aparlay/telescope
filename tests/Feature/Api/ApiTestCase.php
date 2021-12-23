<?php

namespace Aparlay\Core\Tests\Feature\Api;

use Aparlay\Core\Api\V1\Http\Middleware\DeviceIdThrottle;
use Aparlay\Core\Tests\TestCase;
use Artisan;

class ApiTestCase extends TestCase
{
    protected static $isSeeded = false;

    public function setUp(): void
    {
        parent::setUp();

        $this->app->make('config')->set('app.url', env('TEST_DOMAIN'));

        $this->withoutMiddleware(
            DeviceIdThrottle::class
        );
        if (! static::$isSeeded) {
            Artisan::call('db:seed', ['--class' => '\Aparlay\Core\Database\Seeders\DatabaseSeeder', '--database' => 'testing']);
            static::$isSeeded = true;
        }
    }
}
