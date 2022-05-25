<?php

// config for Aparlay/Core/ClassName

return [

    'providers' => [
        /*
         * Application Service Providers...
         */
        \Aparlay\Core\Api\V1\Providers\AuthServiceProvider::class,
        \Aparlay\Core\Admin\Providers\AuthServiceProvider::class,
        \Aparlay\Core\Admin\Providers\AdminServiceProvider::class,
        \Aparlay\Core\Admin\Providers\EventServiceProvider::class,
    ],

    'admin' => [
        'url' => env('ADMIN_DOMAIN', 'https://ltoptop.waptap.dev'),
        'lists' => [
            'page_count' => 20,
            'user_page_count' => 5,
        ],
        'domain' => env('ADMIN_DOMAIN', 'toptop.waptap.dev'),
    ],
];
