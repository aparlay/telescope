<?php

namespace Aparlay\Core\Tests\Feature\Api;

use Aparlay\Core\Tests\TestCase;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApiTestCase extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->app->make('config')->set('app.url', env('TEST_DOMAIN'));
    }
}
