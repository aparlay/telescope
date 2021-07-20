<?php

namespace Aparlay\Core\Tests;

use Aparlay\Core\CoreServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Jenssegers\Mongodb\MongodbServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\LaravelRay\RayServiceProvider;

class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Aparlay\\Core\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            CoreServiceProvider::class,
            MongodbServiceProvider::class,
            RayServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        config()->set('database.connections.testing', [
            'driver' => 'mongodb',
            'dsn' => env('DB_DSN'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', 27011),
            'database' => env('DB_DATABASE', 'alua-v2'),
            'username' => env('DB_USERNAME', 'homestead'),
            'password' => env('DB_PASSWORD', 'secret'),
            'options' => [
                'replicaSet' => env('DB_REPLICASET', 'rs0'),
                'authSource' => env('DB_AUTH_SOURCE', 'admin'),
                'compressors' => env('DB_COMPRESSORS', 'zstd,snappy,zlib'),
                'w' => env('DB_WRITE', 'majority'),
                'wtimeoutMS' => (int)env('DB_WRITE_TIMEOUT', 20000),
                'journal' => (bool)env('DB_JOURNAL', true),
                //'readConcernLevel' => env('DB_READ_CONCERN_LEVEL', 'available'),
                //'readPreferences' => env('DB_READ_PREFERENCES', 'secondaryPreferred'),
                'maxStalenessSeconds' => (int)env('DB_MAX_STALENESS_SECONDS', \MongoDB\Driver\ReadPreference::NO_MAX_STALENESS),
                'connectTimeoutMS' => (int)env('DB_TIMEOUT_CONNECT', 30000),
                'socketTimeoutMS' => (int)env('DB_TIMEOUT_SOCKET', 30000),
            ],
        ]);

        /*
        include_once __DIR__.'/../database/migrations/create_core_table.php.stub';
        (new \CreatePackageTable())->up();
        */
    }
}
