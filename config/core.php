<?php

// config for Aparlay/Core/ClassName
use Aparlay\Core\Api\V1\Providers\AuthServiceProvider;
use Aparlay\Core\Api\V1\Providers\EventServiceProvider;
use Aparlay\Core\Providers\RepositoryServiceProvider;

return [

    'providers' => [
        /*
         * Application Service Providers...
         */
        AuthServiceProvider::class,
        EventServiceProvider::class,
    ],
];
