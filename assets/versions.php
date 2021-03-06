<?php

    if (defined('SP') == false)
        define('SP', DIRECTORY_SEPARATOR);

    if (defined('LOADED') == false)
        exit;

    return [
        'version_current' => '3.5.0',

        'lists' => [
            '3.5.0' => [
                'version'    => '3.5.0',
                'is_beta'    => true,
                'path'       => __DIR__ . SP . '3.5.0',

                'entry_igone_remove' => [
                    'assets' . SP . 'config',
                    'assets' . SP . 'user',
                    'assets' . SP . 'token'
                ]
            ]
        ]
    ];

?>