<?php

// config for Aparlay/Core/ClassName

return [

    'providers' => [
        /*
         * Application Service Providers...
         */
        \Aparlay\Core\Api\V1\Providers\AuthServiceProvider::class,
        \Aparlay\Core\Api\V1\Providers\EventServiceProvider::class,
    ],

    'admin' => [
        'url' => env('ADMIN_DOMAIN', 'https://ltoptop.waptap.dev'),
        'lists' => [
            'page_count' => 20
        ]
    ],

];
