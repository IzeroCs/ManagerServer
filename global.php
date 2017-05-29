<?php

    if (defined('LOADED') == false)
        exit;

    if (defined('SP') == false)
        define('SP', DIRECTORY_SEPARATOR);

    if (defined('ROOT') == false)
        define('ROOT', '.');

    $directory = realpath(ROOT);

    require_once(
        $directory . SP .
        'librarys' . SP .
        'Boot.php'
    );

    $boot = new Librarys\Boot(
        require_once(
            $directory . SP .
            'assets'   . SP .
            'config'   . SP .
            'app.php'
        )
    );

?>