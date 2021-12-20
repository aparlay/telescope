<?php

namespace Aparlay\Core\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    use CreatesApplication;

    public function setUp(): void
    {
        parent::setUp();

        $this->app->make('config')->set('database.default', 'testing');
        $this->app->make('config')->set('app.is_testing', true);

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Aparlay\\Core\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
    }
}
