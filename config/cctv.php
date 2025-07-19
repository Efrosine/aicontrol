<?php

return [

    /*
    |--------------------------------------------------------------------------
    | CCTV Service Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for the external CCTV service
    | integration. The base URL and other settings can be configured here.
    |
    */

    'service' => [
        'base_url' => env('CCTV_SERVICE_BASE_URL', 'http://cctv-service:8000'),
        'timeout' => env('CCTV_SERVICE_TIMEOUT', 30),
        'retry_attempts' => env('CCTV_SERVICE_RETRY_ATTEMPTS', 3),
        'connect_timeout' => env('CCTV_SERVICE_CONNECT_TIMEOUT', 10),
    ],

    /*
    |--------------------------------------------------------------------------
    | API Endpoints
    |--------------------------------------------------------------------------
    |
    | Define the API endpoints for the CCTV service
    |
    */

    'endpoints' => [
        'cameras' => '/cctv',
        'stream' => '/stream',
        'detection_config' => '/detection_config',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Settings
    |--------------------------------------------------------------------------
    |
    | Default settings for CCTV operations
    |
    */

    'defaults' => [
        'stream_quality' => 'high',
        'detection_enabled' => true,
        'recording_enabled' => false,
    ],

];
