<?php

namespace Aparlay\Core\Tests\Feature\Api;

use Aparlay\Core\Tests\TestCase;

class ApiTestCase extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->app->make('config')->set('app.url', env('TEST_DOMAIN'));
    }
}
