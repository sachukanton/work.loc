<?php

    return [
        'default' => env('FILESYSTEM_DRIVER', 'local'),
        'cloud'   => env('FILESYSTEM_CLOUD', 's3'),
        'disks'   => [
            'uploads'       => [
                'driver' => 'local',
                'root'   => public_path('uploads'),
            ],
            'base'          => [
                'driver' => 'local',
                'root'   => public_path(),
            ],
            'config'        => [
                'driver' => 'local',
                'root'   => config_path(),
            ],
            'local'         => [
                'driver' => 'local',
                'root'   => storage_path('app'),
            ],
            'public'        => [
                'driver'     => 'local',
                'root'       => storage_path('app/public'),
                'url'        => env('APP_URL') . '/storage',
                'visibility' => 'public',
            ],
            'product_state' => [
                'driver' => 'local',
                'root'   => storage_path('app/state'),
            ],
            'product_error' => [
                'driver' => 'local',
                'root'   => storage_path('app/error'),
            ],
            'orders_attach' => [
                'driver' => 'local',
                'root'   => storage_path('app/orders_attach'),
            ],
            's3'            => [
                'driver' => 's3',
                'key'    => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
                'region' => env('AWS_DEFAULT_REGION'),
                'bucket' => env('AWS_BUCKET'),
                'url'    => env('AWS_URL'),
            ],
        ],
    ];
