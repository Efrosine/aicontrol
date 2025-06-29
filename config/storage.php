<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Storage Service Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for the MinIO storage service
    | integration. Configure the endpoint and credentials here.
    |
    */

    'minio' => [
        'endpoint' => env('MINIO_ENDPOINT', 'localhost:9000'),
        'access_key' => env('MINIO_ACCESS_KEY', 'minioadmin'),
        'secret_key' => env('MINIO_SECRET_KEY', 'minioadmin'),
        'region' => env('MINIO_REGION', 'us-east-1'),
        'bucket' => env('MINIO_BUCKET', 'detection-archive'),
        'use_ssl' => env('MINIO_USE_SSL', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Archive Settings
    |--------------------------------------------------------------------------
    |
    | Settings for detection archive storage and retrieval
    |
    */

    'archive' => [
        'retention_days' => env('ARCHIVE_RETENTION_DAYS', 30),
        'max_file_size' => env('ARCHIVE_MAX_FILE_SIZE', '100MB'),
        'allowed_types' => ['jpg', 'jpeg', 'png', 'mp4', 'avi', 'mov'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Connection Settings
    |--------------------------------------------------------------------------
    |
    | Settings for MinIO connection
    |
    */

    'connection' => [
        'timeout' => env('MINIO_TIMEOUT', 30),
        'connect_timeout' => env('MINIO_CONNECT_TIMEOUT', 10),
        'retry_attempts' => env('MINIO_RETRY_ATTEMPTS', 3),
    ],

];
