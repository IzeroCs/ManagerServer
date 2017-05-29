<?php

    define('LOADED', 1);
    require_once('global.php');

    $appUpdate = new Librarys\App\AppUpdate(require_once(env('app.path.versions')));

    if ($appUpdate->checkUpdate())
        $appUpdate->responseUpdateResult();
    else
        $appUpdate->responseUpdateError();

?>