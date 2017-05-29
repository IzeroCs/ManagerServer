<?php if (!defined('LOADED')) exit(0); ?>
<?php requireDefine('asset'); ?>

<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $title; ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex, nofollow, noodp, nodir"/>
        <meta http-equiv="Cache-Control" content="private, max-age=0, no-cache, no-store, must-revalidate"/>
        <meta http-equiv="Pragma" content="no-cache"/>
        <meta http-equiv="Expires" content="Thu, 01 Jan 1970 00:00:00 GMT">

        <?php if (isset($themes) == false || is_array($themes) == false) { ?>
            <?php $themes = array(); ?>
        <?php } ?>

        <?php array_unshift($themes, env('resource.theme.icomoon')); ?>
        <?php array_unshift($themes, env('resource.theme.app')); ?>

        <?php if (isset($themes) && is_array($themes)) { ?>
            <?php if (env('app.dev.enable') == false || env('app.dev.compress_css')) { ?>
                <?php $themeInline = null; ?>
                <?php $themeCount  = count($themes); ?>

                <?php for ($i = 0; $i < $themeCount; ++$i) { ?>
                    <?php $themeInline .= str_ireplace('.css', null, basename($themes[$i])); ?>

                    <?php if ($i + 1 < $themeCount) { ?>
                        <?php $themeInline .= '.'; ?>
                    <?php } ?>
                <?php } ?>

                <?php
                        $themeUrl = env('app.http.host') . '/asset.php' .
                                    '?' . ASSET_PARAMETER_THEME_URL .
                                    '=' . $appConfig->get('theme.directory') .
                                    '&' . ASSET_PARAMETER_CSS_URL .
                                    '=' . $themeInline .
                                    '&' . $boot->getCFSRToken()->getName() .
                                    '=' . $boot->getCFSRToken()->getToken() .
                                    '&' . ASSET_PARAMETER_RAND_URL .
                                    '=' . env('dev.rand');
                ?>

                <link rel="stylesheet" type="text/css" href="<?php echo $themeUrl; ?>" media="all,handheld" />

                <?php unset($themeInline); ?>
                <?php unset($themeCount); ?>
                <?php unset($themeUrl); ?>
            <?php } else { ?>
                <?php foreach ($themes AS $entry) { ?>
                    <?php $entry = str_ireplace('.css', null, basename($entry)); ?>
                    <?php
                            $entryUrl = env('app.http.host') . '/asset.php' .
                                        '?' . ASSET_PARAMETER_THEME_URL .
                                        '=' . $appConfig->get('theme.directory') .
                                        '&' . ASSET_PARAMETER_CSS_URL .
                                        '=' . $entry .
                                        '&' . $boot->getCFSRToken()->getName() .
                                        '=' . $boot->getCFSRToken()->getToken() .
                                        '&' . ASSET_PARAMETER_RAND_URL .
                                        '=' . env('dev.rand');
                    ?>

                    <link rel="stylesheet" type="text/css" href="<?php echo $entryUrl; ?>" media="all,handheld" />
                    <?php unset($entryUrl); ?>
                <?php } ?>
            <?php } ?>
            <?php unset($themes); ?>
        <?php } ?>

        <link rel="icon" type="image/png" href="<?php echo env('resource.icon.favicon_png'); ?>"/>
        <link rel="icon" type="image/x-icon" href="<?php echo env('resource.icon.favicon_ico'); ?>"/>
        <link rel="shortcut icon" type="image/x-icon" href="<?php echo env('resource.icon.favicon_ico'); ?>"/>

        <?php if (isset($scripts) == false || is_array($scripts) == false) { ?>
            <?php $scripts = array(); ?>
        <?php } ?>

        <?php if ($appConfig->get('enable_disable.auto_focus_input_last') == true) { ?>
            <?php array_unshift($scripts, env('resource.javascript.auto_focus_input_last')); ?>
        <?php } ?>

        <?php if ($appConfig->get('enable_disable.button_save_on_javascript') == true) { ?>
            <?php array_unshift($scripts, env('resource.javascript.button_save_on_javascript')); ?>
        <?php } ?>

        <?php array_unshift($scripts, env('resource.javascript.on_load')); ?>

        <?php if (isset($scripts) && is_array($scripts)) { ?>
            <?php if (env('app.dev.enable') == false || env('app.dev.compress_js')) { ?>
                <?php $scriptInline = null; ?>
                <?php $scriptCount  = count($scripts); ?>

                <?php for ($i = 0; $i < $scriptCount; ++$i) { ?>
                    <?php $scriptInline .= str_ireplace('.js', null, basename($scripts[$i])); ?>

                    <?php if ($i + 1 < $scriptCount) { ?>
                        <?php $scriptInline .= '.'; ?>
                    <?php } ?>
                <?php } ?>

                <?php
                        $scriptUrl = env('app.http.host') . '/asset.php' .
                                    '?' . ASSET_PARAMETER_JS_URL .
                                    '=' . $scriptInline .
                                    '&' . $boot->getCFSRToken()->getName() .
                                    '=' . $boot->getCFSRToken()->getToken() .
                                    '&' . ASSET_PARAMETER_RAND_URL .
                                    '=' . env('dev.rand');
                ?>

                <script type="text/javascript" src="<?php echo $scriptUrl; ?>"></script>

                <?php unset($scriptInline); ?>
                <?php unset($scriptCount); ?>
                <?php unset($scriptUrl); ?>
            <?php } else { ?>
                <?php foreach ($scripts AS $entry) { ?>
                    <?php $entry = str_ireplace('.js', null, basename($entry)); ?>
                    <?php
                            $entryUrl = env('app.http.host') . '/asset.php' .
                                        '?' . ASSET_PARAMETER_JS_URL .
                                        '=' . $entry .
                                        '&' . $boot->getCFSRToken()->getName() .
                                        '=' . $boot->getCFSRToken()->getToken() .
                                        '&' . ASSET_PARAMETER_RAND_URL .
                                        '=' . env('dev.rand');
                    ?>

                    <script type="text/javascript" src="<?php echo $entryUrl; ?>"></script>
                    <?php unset($entryUrl); ?>
                <?php } ?>
            <?php } ?>
            <?php unset($scripts); ?>
        <?php } ?>

    </head>
    <body>
        <div id="container">
            <div id="header">
                <div id="logo">
                    <a href="<?php echo env('app.http.host'); ?>">
                        <span id="logo" class="icomoon icon-home"></span>
                    </a>
                </div>
                <ul id="action">
                    <li>
                        <a href="#">
                            <span class="icomoon icon-search"></span>
                        </a>
                    </li>
                    <?php if ($appUser->isLogin()) { ?>
                        <?php $url    = env('app.http.host') . '/mysql'; ?>
                        <?php $isShow = true; ?>

                        <?php if ($appMysqlConfig->get('mysql_is_connect', false)) { ?>
                           <?php if ($appMysqlConfig->get('mysql_name', null) == null) { ?>
                                <?php $url   .= '/list_database.php'; ?>
                                <?php $isShow = defined('MYSQL_LIST_DATABASE') == false; ?>
                            <?php } else { ?>
                                <?php $url   .= '/list_table.php'; ?>
                                <?php $isShow = defined('MYSQL_LIST_TABLE') == false; ?>
                            <?php } ?>
                        <?php } else if (defined('MYSQL_REQUIRE')) { ?>
                            <?php $isShow = false; ?>
                        <?php } ?>

                        <?php if ($isShow) { ?>
                            <li>
                                <a href="<?php echo $url; ?>">
                                    <span class="icomoon icon-mysql"></span>
                                </a>

                            </li>
                        <?php } ?>
                        <?php unset($url); ?>
                        <?php unset($isShow); ?>

                        <?php if (defined('SETTING') == false) { ?>
                            <li>
                                <a href="<?php echo env('app.http.host'); ?>/system/setting.php">
                                    <span class="icomoon icon-config"></span>
                                </a>
                            </li>
                        <?php } ?>
                        <li class="about">
                            <a href="<?php echo env('app.http.host'); ?>/app/about.php">
                                <span class="icomoon icon-about"></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo env('app.http.host'); ?>/user/exit.php">
                                <span class="icomoon icon-exit"></span>
                            </a>
                        </li>
                    <?php } else { ?>
                        <li>
                            <a href="<?php echo env('app.http.host'); ?>/about.php">
                                <span class="icomoon icon-about"></span>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
            <div id="content">
