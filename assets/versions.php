<?php

    if (defined('SP') == false)
        define('SP', DIRECTORY_SEPARATOR);

    return [
        'version_current' => '3.5.0',

        'lists' => [
            '3.5.0' => [
                'version'         => '3.5.0',
                'is_beta'         => true,
                'changelog'       => null,
                'build_last'      => time(),
                'compress_method' => 'zip',
                'path'            => __DIR__ . SP . '3.5.0'
            ]
        ]
    ];

?>