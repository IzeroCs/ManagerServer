<?php

    return [
        'server' => [
            'document_root' => dirname(dirname(dirname(__DIR__))),
        ],

        'app' => [
            'sleep_time_redirect' => 3,

            'dev' => [
                'enable'       => true,
                'compress_css' => false,
                'compress_js'  => false,
                'cache_css'    => 86400,
                'cache_js'     => 86400,
                'rand'         => 'generatorDevRandResource',
            ],

            'date' => [
                'timezone' => 'Asia/Ho_Chi_Minh'
            ],

            'autoload' => [
                'prefix_namespace' => 'Librarys',
                'prefix_class_mime' => '.php'
            ],

            'session' => [
                'init'            => true,
                'name'            => 'ManagerIzeroCs',
                'cookie_lifetime' => 86400 * 7,
                'cookie_path'     => '/${app.directory_absolute_http}/',
                'cache_limiter'   => 'private',
                'cache_expire'    => 0
            ],

            'path' => [
                'root'         => dirname(dirname(__DIR__)),
                'librarys'     => '${app.path.root}${SP}librarys',
                'resource'     => '${app.path.root}${SP}assets',
                'error'        => '${app.path.resource}${SP}error',
                'theme'        => '${app.path.resource}${SP}theme',
                'icon'         => '${app.path.resource}${SP}icon',
                'javascript'   => '${app.path.resource}${SP}javascript',
                'template'     => '${app.path.resource}${SP}template',
                'lang'         => '${app.path.resource}${SP}language',
                'user'         => '${app.path.resource}${SP}user',
                'config'       => '${app.path.resource}${SP}config',
                'define'       => '${app.path.resource}${SP}define',
                'cache'        => '${app.path.resource}${SP}cache',
                'tmp'          => '${app.path.resource}${SP}tmp',
                'backup'       => '${app.path.resource}${SP}backup',
                'backup_mysql' => '${app.path.backup}${SP}mysql'
            ],

            'http' => [

            ],

            'language' => [
                'path'   => '${app.path.resource}${SP}language',
                'mime'   => '.php',
                'locale' => 'vi'
            ],

            'tmp' => [
                'lifetime' => 180,
                'limit'    => 20
            ],

            'firewall' => [
                'path'            => '${app.path.resource}${SP}firewall',
                'path_htaccess'   => '${app.path.root}${SP}.htaccess',
                'email'           => 'Izero.Cs@gmail.com',
                'enable'          => false,
                'enable_htaccess' => true,

                'time' => [
                    'request' => 1,
                    'small'   => 10,
                    'medium'  => 120,
                    'large'   => 3600
                ],

                'lock_count' => [
                    'small'    => 5,
                    'medium'   => 10,
                    'large'    => 15,
                    'forever'  => 20,
                    'htaccess' => 25
                ]
            ],

            'cfsr' => [
                'use_token'   => true,
                'key_name'    => '_cfsr_token',
                'time_live'   => 60000,
                'path_cookie' => '/${app.directory_absolute_http}/',

                'validate_post' => true,
                'validate_get'  => true
            ],

            'login' => [
                'session_login_name' => 'LOGIN_MANAGER',
                'session_token_name' => 'LOGIN_TOKEN_MANAGER'
            ]
        ],

        'error' => [
            'reporting' => E_ALL | E_STRICT,
            'mime'      => '.php',
            'theme'     => '${resource.theme.default}',

            'handler'   => 'handler',
            'not_found' => 'not_found',
            'firewall'  => 'firewall'
        ],

        'resource' => [
            'config' => [
                'about'            => '${app.path.config}${SP}about.php',
                'manager'          => '${app.path.config}${SP}manager.php',
                'manager_disabled' => '${app.path.config}${SP}manager_disabled.php',
                'user'             => '${app.path.config}${SP}user.php',
                'mysql'            => '${app.path.config}${SP}mysql.php'
            ],

            'define' => [
                'alert' => '${app.path.define}${SP}alert.php'
            ],

            'javascript' => [
                'on_load'                   => '${app.http.javascript}/on_load.js',
                'custom_input_file'         => '${app.http.javascript}/custom_input_file.js',
                'more_input_url'            => '${app.http.javascript}/more_input_url.js',
                'chmod_input'               => '${app.http.javascript}/chmod_input.js',
                'button_save_on_javascript' => '${app.http.javascript}/button_save_on_javascript.js',
                'auto_focus_input_last'     => '${app.http.javascript}/auto_focus_input_last.js',
                'checkbox_checkall'         => '${app.http.javascript}/checkbox_checkall.js'
            ],

            'theme' => [
                'path' => [
                        'default' => '${app.http.theme}/default'
                ],

                'app'     => '${app.http.theme}/default/theme.css',
                'about'   => '${app.http.theme}/default/about.css',
                'login'   => '${app.http.theme}/default/login.css',
                'file'    => '${app.http.theme}/default/file.css',
                'mysql'   => '${app.http.theme}/default/mysql.css',
                'icomoon' => '${app.http.theme}/default/style.css'
            ],

            'icon' => [
                'favicon_ico' => '${app.http.theme}/default/icon/icon.ico',
                'favicon_png' => '${app.http.theme}/default/icon/icon.png'
            ]
        ],

        'config_file_name' => [
        	'about'   => 'about.php',
            'manager' => 'manager.php',
            'mysql'   => 'mysql.php'
        ]
    ];

?>
