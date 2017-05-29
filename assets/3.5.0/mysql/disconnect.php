<?php

    define('LOADED', 1);
    require_once('global.php');

    if ($appUser->isLogin() == false)
        $appAlert->danger(lng('login.alert.not_login'), ALERT_LOGIN, env('app.http.host') . '/user/login.php');

    if ($appMysqlConfig->set('mysql_is_connect', false)) {
        $appMysqlConfigWrite = new Librarys\App\Mysql\AppMysqlConfigWrite($appMysqlConfig);
        $appMysqlConfigWrite->setSpacing('    ');

        if ($appMysqlConfigWrite->write()) {
            $boot->sleepFixHeaderRedirectUrl();
            $appAlert->success(lng('mysql.home.alert.disconnect_success'), ALERT_MYSQL_HOME, 'index.php');
        }
    }

    $appAlert->success(lng('mysql.home.alert.disconnect_failed'), ALERT_MYSQL_HOME, 'list_database.php');

?>