<?php

namespace Aparlay\Core\Tests\Feature\Api;

use Aparlay\Core\Tests\TestCase;
use Artisan;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApiTestCase extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->app->make('config')->set('app.is_testing', true);
        $this->app->make('config')->set('app.url', env('TEST_DOMAIN'));

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Aparlay\\Core\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );

        Artisan::call('db:seed', ['--class' => '\Aparlay\Core\Database\Seeders\DatabaseSeeder', '--database' => 'testing']);
    }
}
