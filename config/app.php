<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    'asset_url' => env('ASSET_URL', null),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => 'UTC',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Faker Locale
    |--------------------------------------------------------------------------
    |
    | This locale will be used by the Faker PHP library when generating fake
    | data for your database seeds. For example, this will be used to get
    | localized telephone numbers, street address information and more.
    |
    */

    'faker_locale' => 'en_US',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => [

        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,

        /*
         * Package Service Providers...
         */

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\HorizonServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
        App\Providers\TelescopeServiceProvider::class
    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => [

        'App' => Illuminate\Support\Facades\App::class,
        'Arr' => Illuminate\Support\Arr::class,
        'Artisan' => Illuminate\Support\Facades\Artisan::class,
        'Auth' => Illuminate\Support\Facades\Auth::class,
        'Blade' => Illuminate\Support\Facades\Blade::class,
        'Broadcast' => Illuminate\Support\Facades\Broadcast::class,
        'Bus' => Illuminate\Support\Facades\Bus::class,
        'Cache' => Illuminate\Support\Facades\Cache::class,
        'Config' => Illuminate\Support\Facades\Config::class,
        'Cookie' => Illuminate\Support\Facades\Cookie::class,
        'Crypt' => Illuminate\Support\Facades\Crypt::class,
        'Date' => Illuminate\Support\Facades\Date::class,
        'DB' => Illuminate\Support\Facades\DB::class,
        'Eloquent' => Illuminate\Database\Eloquent\Model::class,
        'Event' => Illuminate\Support\Facades\Event::class,
        'File' => Illuminate\Support\Facades\File::class,
        'Gate' => Illuminate\Support\Facades\Gate::class,
        'Hash' => Illuminate\Support\Facades\Hash::class,
        'Http' => Illuminate\Support\Facades\Http::class,
        'Lang' => Illuminate\Support\Facades\Lang::class,
        'Log' => Illuminate\Support\Facades\Log::class,
        'Mail' => Illuminate\Support\Facades\Mail::class,
        'Notification' => Illuminate\Support\Facades\Notification::class,
        'Password' => Illuminate\Support\Facades\Password::class,
        'Queue' => Illuminate\Support\Facades\Queue::class,
        'RateLimiter' => Illuminate\Support\Facades\RateLimiter::class,
        'Redirect' => Illuminate\Support\Facades\Redirect::class,
        // 'Redis' => Illuminate\Support\Facades\Redis::class,
        'Request' => Illuminate\Support\Facades\Request::class,
        'Response' => Illuminate\Support\Facades\Response::class,
        'Route' => Illuminate\Support\Facades\Route::class,
        'Schema' => Illuminate\Support\Facades\Schema::class,
        'Session' => Illuminate\Support\Facades\Session::class,
        'Storage' => Illuminate\Support\Facades\Storage::class,
        'Str' => Illuminate\Support\Str::class,
        'URL' => Illuminate\Support\Facades\URL::class,
        'Validator' => Illuminate\Support\Facades\Validator::class,
        'View' => Illuminate\Support\Facades\View::class,

    ],

    'adminEmail' => 'admin@waptap.com',
    'supportEmail' => 'support@waptap.com',
    'senderEmail' => 'noreply@waptap.com',
    'senderName' => 'Waptap.com mailer',
    'cache' => [
        'veryLongDuration' => 432000,
        'longDuration' => 86400,
        'mediumDuration' => 3600,
        'shortDuration' => 180,
    ],
    'user.passwordResetTokenExpire' => 3600,
    'user.passwordMinLength' => 8,
    'user.defaultSetting' => [
        'otp' => false,
        'notifications' => [
            'unread_message_alerts' => false,
            'new_followers' => false,
            'news_and_updates' => false,
            'tips' => false,
            'new_subscribers' => false
        ]
    ],
    'protected_video_ids' => [
        // QA on dev server
        '606fead5d0e08b58c61ad6e3',
        '608199520e3af6381e04f983',

        '606ae93b430ae971976d01b9',
        '606ae98c77e4686a7442cfeb',
        '606ae9be77e4686a7442cfec',
        '606aea86430ae971976d01ba',
        '606aea9e8641010c8326c988',
        '606aeb758641010c8326c989',
        '606aebec8641010c8326c98a',
        '606aec25430ae971976d01bb',
        '606aec4e430ae971976d01bc',
        '606aed9c8641010c8326c98b',
        '606af0e077e4686a7442cfee',
        '606af82e7768ee4102186d13',
        '606af94d77e4686a7442cfef',
        '606af9ca77e4686a7442cff0',
        '606afa1f8641010c8326c98d',
        '606afa8a8641010c8326c98e',
        '606afb47430ae971976d01bd',
        '606b1a921a9fb26779332683',

        // licensed
        '606af9f38641010c8326c98c',
        '606e107db7f64e192e2001ff',
        '606b53786cc43568527fefd4',
        '606e88446cc43568527fefda',
        '60c71274e56664179e4e5ff8',
        '60c6f11cdede601197082643',
        '60c7b03d6bd8dc139b19a2d5',
        '60c7afa26bd8dc139b19a2d4',
        '60c7ad2b6bd8dc139b19a2d3',
        '60c71e3ce56664179e4e5ff9',
        '60c7b2103f4dcf491b454704',
        '60c6f371508c513880000963',
        '60c70685e56664179e4e5ff6',
        '60c6ef00e56664179e4e5ff3',
        '60c70078e56664179e4e5ff4',
        '60c7025fdede601197082645',
        '60c7037fe56664179e4e5ff5',
        '60c70404dede601197082646',
        '606e943c36f67f661a2fee6e',
        '60c70ad9dede601197082648',
        '606e8b5c36f67f661a2fee6b',
        '60c7256bdede60119708264a',
        '60c6f839dede601197082644',
        '60c70a20e56664179e4e5ff7',
        '606f139890eb31778f1214b2',
        '60c7b12684ab325050109593',
        '606af94d77e4686a7442cfef',
        '606aeadf77e4686a7442cfed',
        '606e818a430ae971976d01c5',
        '60c717e3dede601197082649',
        '60c6fc65508c513880000964',
        '606e88aa430ae971976d01cb',
        '60c7ae8a3f4dcf491b454703',
        '60c70868dede601197082647',
    ],

    'domain' => 'waptap.test',
    'frontendUrl' => 'https://www.waptap.test',
    'adminUrls' => [
        'profile' => 'https://admin.waptap.dev/user/view?id=',
        'media' => 'https://admin.waptap.dev/media/view?id='
    ],
    'reactUrls' => [
        'profile' => 'https://app.waptap.dev/profile/',
        'share' => 'https://app.waptap.dev/s/',
        'home' => 'https://app.waptap.dev',
    ],
    'otp' => [
        'length' => [
            'min' => 100000,
            'max' => 999999
        ],
        'duration' => 600,
    ],
    'dbQueryCacheDuration' => 3600,
    'uploadUrl' => 'https://upload.waptap.dev',
    'sms' => [
        'numbers' => ['+9810008284', '+9820000110220'],
        'messages' => [
            'otp' => "Your OTP code to use in Waptap application:\n\n%token%"
        ],
    ],
    'email' => [
        'noreply' => 'noreply@alua.com'
    ],
    'bunny' => [
        'asset' => [
            'hostname' => 'storage.bunnycdn.com',
            'port' => 21,
            'passive' => true,
            'root' => '/waptap-assets-dev/',
            'username' => 'waptap-assets-dev',
            'password' => 'e070afb6-7aa2-44ee-a8f5b61b7f53-9e08-4a81',
        ],
        'web' => [
            'hostname' => 'storage.bunnycdn.com',
            'port' => 21,
            'passive' => true,
            'root' => '/waptap-video-dev/',
            'username' => 'waptap-video-dev',
            'password' => 'dd6dc143-d29a-47b5-a9edd525cbdb-088a-4d65',
        ],
        'video' => [
            'hostname' => 'la.storage.bunnycdn.com',
            'port' => 21,
            'passive' => true,
            'root' => '/waptap-web-dev/',
            'username' => 'waptap-web-dev',
            'password' => 'db11093d-e093-4287-8ffc59d7f534-dd91-4115',
        ]
    ],
    'gc' => [
        'avatars' => [
            'bucket' => 'cdn.waptap.dev',
            'root' => 'avatars/',
        ],
        'videos' => [
            'bucket' => 'cdn.waptap.dev',
            'root' => 'videos/',
        ],
        'covers' => [
            'bucket' => 'cdn.waptap.dev',
            'root' => 'covers/',
        ],
        'project_id' => 'waptap-dev',
        'keyFile' => dirname(__DIR__, 2) . '/common/key/gc_key.json'
    ],
    'cdn' => [
        'videos' => 'https://vcdn.waptap.dev/',
        'covers' => 'https://acdn.waptap.dev/covers/',
        'avatars' => 'https://acdn.waptap.dev/avatars/',
    ],
    'backblaze' => [
        'applicationKeyId' => '0000a6daeaf64b40000000005',
        'applicationKey' => 'K000gAHMKyTClyfAErEAafQPgrOCV8o',
        'accountId' => '0a6daeaf64b4', // master
        'masterKey' => '0002bf7f0b1002d4fcc22a986f1aece0929ba49045', // master
        's3Url' => 'https://s3.us-west-000.backblazeb2.com',
        'videoBucketName' => 'waptap-videos-prod',
        'videoBucketId' => 'd08a76ed1a0e8a2f76840b14',
        'avatarBucketName' => 'waptap-avatars-prod',
        'avatarBucketId' => 'f01ac60d1a0e8a2f76840b14',
        'avatars' => [
            'bucket' => 'waptap-avatars-prod',
            'bucket_id' => 'f01ac60d1a0e8a2f76840b14',
        ],
        'videos' => [
            'bucket' => 'waptap-videos-prod',
            'bucket_id' => 'd08a76ed1a0e8a2f76840b14',
        ],
    ],
    'linode' => [
        'storage' => [
            'key' => 'UBN7G4X5DQ81T0JSL8XA',
            'secret' => 'lKgPgPFzwRMgxZX7oCvYvFJTkLMZ9teWqnTFTBL7',
            's3Url' => 'https://us-east-1.linodeobjects.com',
            'bucket' => 'upload',
            'region' => 'us-east-1',
            'videos' => [
                'root' => 'videos/',
            ],
            'avatars' => [
                'root' => 'avatars/',
            ]
        ]
    ],
    'media' => [
        'grpc' => '94.130.236.202:9001',
        'path' => '/var/www/uploads/staging/',
        'ftp' => [
            'host' => '94.130.236.202',
            'port' => 5411,
            'username' => 'sftp_staging',
            'password' => 'QaldeNCNXWOs%mcpMu*VS*Hz',
            'root' => '/staging',
            'timeout' => 100,
            'permPublic' => 0777
        ]
    ],
    'jwt' => [
        'id' => '4f1g23a12aa',
        'enc' => [
            'algorithm' => 'A256KW',
            'encryption' => 'A256CBC-HS512',
            'compression' => 'DEF',
            'keyId' => 'encKey4f1g23a12aa',
            'issuer' => 'https://api.waptap.com',
            'audience' => [
                'https://api.waptap.com',
                'https://ws.waptap.com',
                'https://www.waptap.com',
                'https://app.waptap.com',
                'https://waptap.com',
                'https://api.waptap.dev',
                'https://ws.waptap.dev',
                'https://www.waptap.dev',
                'https://app.waptap.dev',
                'https://waptap.dev',
                'https://api.waptap.test',
                'https://ws.waptap.test',
                'https://www.waptap.test',
                'https://app.waptap.test',
                'https://waptap.test'
            ],
            'application' => 'waptap',
            'use' => 'enc',
            'notBefore' => 0, // now!
            'tokenExpireTTL' => 3600, // one hour
            'refreshExpireTTL' => 2592000, // one month
            'secret' => 'yFxcCWl8ZQlUfzHFrxUUQZgyytSb7KJlz04laQoFAqC099JTBu6HKx8ttZivIxKu',
        ],
        'sig' => [
            'algorithm' => 'HS512',
            'compression' => 'DEF',
            'keyId' => 'sigKey4f1g23a12aa',
            'issuer' => 'https://api.waptap.com',
            'audience' => [
                'https://api.waptap.com',
                'https://ws.waptap.com',
                'https://www.waptap.com',
                'https://app.waptap.com',
                'https://waptap.com',
                'https://api.waptap.dev',
                'https://ws.waptap.dev',
                'https://www.waptap.dev',
                'https://app.waptap.dev',
                'https://waptap.dev',
                'https://api.waptap.test',
                'https://ws.waptap.test',
                'https://www.waptap.test',
                'https://app.waptap.test',
                'https://waptap.test'
            ],
            'application' => 'waptap',
            'use' => 'sig',
            'notBefore' => 0, // now!
            'tokenExpireTTL' => 3600, // one hour
            'refreshExpireTTL' => 2592000, // one month
            'secret' => 'yFxcCWl8ZQlUfzHFrxUUQZgyytSb7KJlz04laQoFAqC099JTBu6HKx8ttZivIxKu',
        ],
    ],
    'websocket' => [
        'host'   => '127.0.0.1',
        'port' => 3333,
    ],
];
