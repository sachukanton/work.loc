<?php

    return [
        'defaults'  => [
            'guard'     => 'web',
            'passwords' => 'users',
        ],
        'guards'    => [
            'web' => [
                'driver'   => 'session',
                'provider' => 'users',
            ],

            'api' => [
                'driver'   => 'token',
                'provider' => 'users',
                'hash'     => FALSE,
            ],
        ],
        'providers' => [
            'users' => [
                'driver' => 'eloquent',
                'model'  => App\Models\User\User::class,
            ],

            // 'users' => [
            //     'driver' => 'database',
            //     'table' => 'users',
            // ],
        ],
        'passwords' => [
            'users' => [
                'provider' => 'users',
                'table'    => 'password_resets',
                'expire'   => 60,
            ],
        ],
    ];
