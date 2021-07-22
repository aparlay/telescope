<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('DB_CONNECTION', 'mongodb'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DATABASE_URL'),
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ],

        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter(
                [
                    PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
                ]
            ) : [],
        ],

        'pgsql' => [
            'driver' => 'pgsql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'schema' => 'public',
            'sslmode' => 'prefer',
        ],

        'sqlsrv' => [
            'driver' => 'sqlsrv',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '1433'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
        ],

        'mongodb' => [
            'driver' => 'mongodb',
            'dsn' => env('DB_DSN'),
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
        ],

        'testing' => [
            'driver' => 'mongodb',
            'dsn' => env('DB_DSN'),
            'database' => env('DB_DATABASE_TEST', 'alua-v2-testing'),
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
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer body of commands than a typical key-value system
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'alua'), '_') . '_database_'),
        ],

        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
            'read_timeout' => 60,
        ],

        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_CACHE_DB', '1'),
            'read_timeout' => 60,
        ],

    ],

];
