<?php

return [

    'zkservice' => [
        'base_url' => env('ZKSERVICE_URL', 'http://zkservice:8001'), // Lee del .env, si no existe usa el valor de Docker
        'api_key' => env('ZKSERVICE_API_KEY'),
    ],

];
