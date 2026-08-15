<?php

return [

    'auth' => [

        'guard' => env('DOMAIN_GENERATOR_GUARD', 'api'),

        'login_field' => env('DOMAIN_GENERATOR_LOGIN_FIELD', 'email'),

    ],

    'identifier' => [

        'column' => 'hash',

        'strategy' => env('DOMAIN_GENERATOR_IDENTIFIER', 'ulid'),

    ],

    'domain_folder' => env('APP_DOMAIN_FOLDER', 'Domain'),

];