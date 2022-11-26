<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Routes Configuration
    |--------------------------------------------------------------------------
    |
    | These configuration options define the settings for a route group
    |
    */
    'routes' => [
        'prefix' => 'tgbotapi',
        'middleware' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Api Server Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration options define the default Telegram Bot API server
    |
    */
    'api_url' => 'https://api.telegram.org/bot{TOKEN}/{METHOD}',

    /*
    |--------------------------------------------------------------------------
    | Offset Store Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration option defines the implementation for 
    | storing the Telegram Update Offset
    |
    | Supported: "file"
    |
    */
    'offset_store' => 'file',

    /*
    |--------------------------------------------------------------------------
    | Http Client Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration option specifies the HTTP client implementation to use
    |
    | Supported: "laravel"
    |
    */
    'http_client' => 'laravel',

    /*
    |--------------------------------------------------------------------------
    | Bots Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration option contains settings for all of your bots used 
    | in the application
    |
    */
    'bots' => [
        [
            'username' => '',
            'token' => '',
            'middleware' => [],
            'log_channel' => 'stack',
            'api_url' => null,
        ],
    ],

];
