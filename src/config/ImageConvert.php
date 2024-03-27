<?php

return [

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

    'do_spaces'    => [
        'driver'         => 's3',
        'key'            => env('DO_KEY'),
        'secret'         => env('DO_SECRET'),
        'region'         => env('DO_REGION'),
        'bucket'         => env('DO_BUCKET'),
        "endpoint"       => env("DO_ENPOINT"),
        "originendpoint" => env("ORIGIN_ENDPOINT"), //full end point of the do spaces
    ],

    
];