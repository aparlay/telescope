<?php

namespace Aparlay\Core\Tests\Feature\Api;

use Aparlay\Core\Api\V1\Http\Middleware\DeviceIdThrottle;
use Aparlay\Core\Tests\TestCase;

class ApiTestCase extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(
            DeviceIdThrottle::class
        );
    }
}
