<?php

namespace Aparlay\Core\Tests;

use Faker\Generator;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Artisan;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    use CreatesApplication;

    /**
     * @var Generator
     */
    protected $faker;
    protected static $seedCoreDB     = false;
    protected static $truncateCoreDB = false;

    public function setUp(): void
    {
        parent::setUp();
        $this->baseUrl = env('TEST_DOMAIN');

        $this->faker   = app('Faker');

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Aparlay\\Core\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
        );

        if (static::$truncateCoreDB) {
            Artisan::call('db:seed', ['--class' => '\Aparlay\Core\Database\Seeders\DatabaseTruncate', '--database' => 'testing']);
            static::$truncateCoreDB = false;
        }
        if (static::$seedCoreDB) {
            Artisan::call('db:seed', ['--class' => '\Aparlay\Core\Database\Seeders\DatabaseSeeder', '--database' => 'testing']);
            static::$seedCoreDB = false;
        }
    }
}
