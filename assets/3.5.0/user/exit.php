<?php

    define('LOADED', 1);
    define('ROOT',   '..' . DIRECTORY_SEPARATOR);

    require_once(ROOT . 'incfiles' . DIRECTORY_SEPARATOR . 'global.php');

    if ($appUser->isLogin()) {
    	if (session_destroy())
    		$appAlert->success(lng('login.alert.exit_session_success'), ALERT_LOGIN, 'login.php');
        else
        	$appAlert->danger(lng('login.alert.exit_session_failed'), ALERT_INDEX, env('app.http.host'));
    } else {
        $appAlert->danger(lng('login.alert.not_login'), ALERT_LOGIN, 'login.php');
    }

?>