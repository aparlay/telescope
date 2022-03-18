<?php

namespace Aparlay\Core\Tests;

use Faker\Generator;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Artisan;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    use CreatesApplication;

    /** @var Generator  */
    protected $faker;

    protected static $isSeeded = false;

    public function setUp(): void
    {
        parent::setUp();

        $this->faker = app('Faker');

        $this->app->make('config')->set('database.default', 'testing');
        $this->app->make('config')->set('app.is_testing', true);
        $this->app->make('config')->set('app.debug', false);

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Aparlay\\Core\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );

        if (! static::$isSeeded) {
            Artisan::call('db:seed', ['--class' => '\Aparlay\Payout\Database\Seeders\DatabaseSeeder', '--database' => 'testing']);
            static::$isSeeded = true;
        }
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
    }
}
