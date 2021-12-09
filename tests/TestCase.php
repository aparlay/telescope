<?php

namespace Aparlay\Core\Tests;

use Aparlay\Core\Api\V1\Http\Middleware\DeviceIdThrottle;
use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    use CreatesApplication;


    public function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(
            DeviceIdThrottle::class
        );

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Aparlay\\Core\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
    }
}
