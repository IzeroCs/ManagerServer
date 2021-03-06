<?php

    if (defined('LOADED') == false)
        exit;

    return [
        'server' => [
            'document_root' => dirname(dirname(dirname(__DIR__))),
        ],

        'app' => [
            'dev' => [
                'enable' => true
            ],

            'date' => [
                'timezone' => 'Asia/Ho_Chi_Minh'
            ],

            'autoload' => [
                'prefix_namespace' => 'Librarys',
                'prefix_class_mime' => '.php'
            ],

            'session' => [
                'init'            => false,
                'name'            => 'ServerManagerIzeroCs',
                'cookie_lifetime' => 86400 * 7,
                'cookie_path'     => '/${app.directory_absolute_http}/',
                'cache_limiter'   => 'private',
                'cache_expire'    => 0
            ],

            'path' => [
                'root'     => dirname(dirname(__DIR__)),
                'librarys' => '${app.path.root}${SP}librarys',
                'resource' => '${app.path.root}${SP}assets',
                'versions' => '${app.path.resource}${SP}versions.php'
            ],

            'cfsr' => [
                'use_token'   => false,
                'key_name'    => '_cfsr_token',
                'time_live'   => 60000,
                'path_cookie' => '/${app.directory_absolute_http}/',

                'validate_post' => true,
                'validate_get'  => true
            ]
        ]

    ];

?>
