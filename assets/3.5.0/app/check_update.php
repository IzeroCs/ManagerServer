<?php

    use Librarys\App\AppAboutConfig;
    use Librarys\App\AppUpdate;
    use Librarys\File\FileCurl;

    define('LOADED',              1);
    define('PARAMETER_CHECK_URL', 'check');

    require_once('global.php');

    $title     = lng('app.check_update.title_page');
    $themes    = [ env('resource.theme.about') ];
    $config    = new AppAboutConfig($boot, env('resource.config.about'));
    $appUpdate = new AppUpdate($config);
    $servers   = $appUpdate->getServers();
    $appAlert->setID(ALERT_APP_CHECK_UPDATE);
    require_once(ROOT . 'incfiles' . SP . 'header.php');

    if (isset($_GET[PARAMETER_CHECK_URL])) {
        if (count($servers) <= 0) {
            $appAlert->danger(lng('app.check_update.alert.not_server_check'));
        } else if ($appUpdate->checkUpdate() === false) {
            $serverErrors = $appUpdate->getServerErrors();

            if (is_array($serverErrors)) {
                foreach ($serverErrors AS $server => $errors) {
                    $errorInt    = $errors[AppUpdate::ARRAY_KEY_ERROR_INT];
                    $errorUrl    = $errors[AppUpdate::ARRAY_KEY_URL];
                    $errorCheck  = $errors[AppUpdate::ARRAY_KEY_ERROR_CHECK];
                    $errorServer = $errors[AppUpdate::ARRAY_KEY_ERROR_SERVER];

                    if ($errorInt === FileCurl::ERROR_URL_NOT_FOUND)
                        $appAlert->danger(lng('app.check_update.alert.address_not_found', 'url', $errorUrl));
                    else if ($errorInt === FileCurl::ERROR_FILE_NOT_FOUND)
                        $appAlert->danger(lng('app.check_update.alert.file_not_found', 'url', $errorUrl));
                    else if ($errorInt === FileCurl::ERROR_AUTO_REDIRECT)
                        $appAlert->danger(lng('app.check_update.alert.auto_redirect_url_failed', 'url', $errorUrl));
                    else if ($errorInt === FileCurl::ERROR_CONNECT_FAILED)
                        $appAlert->danger(lng('app.check_update.alert.connect_url_failed', 'url', $errorUrl));
                    else if ($errorCheck === AppUpdate::ERROR_CHECK_JSON_DATA)
                        $appAlert->danger(lng('app.check_update.alert.error_json_data', 'url', $errorUrl));
                    else if ($errorCheck === AppUpdate::ERROR_CHECK_JSON_DATA_NOT_VALIDATE)
                        $appAlert->danger(lng('app.check_update.alert.error_json_data_not_validate', 'url', $errorUrl));
                    else if ($errorServer === AppUpdate::ERROR_SERVER_NOT_FOUND_LIST_VERSION_IN_SERVER)
                        $appAlert->danger(lng('app.check_update.alert.error_not_found_list_version_in_server', 'url', $errorUrl));
                    else if ($errorServer === AppUpdate::ERROR_SERVER_NOT_FOUND_PARAMETER_VERSION_GUEST)
                        $appAlert->danger(lng('app.check_update.alert.error_not_found_parameter_guest', 'url', $errorUrl));
                    else if ($errorServer === AppUpdate::ERROR_SERVER_VERSION_GUEST_NOT_VALIDATE)
                        $appAlert->danger(lng('app.check_update.alert.error_version_guest_not_validate', 'url', $errorUrl));
                    else if ($errorServer === AppUpdate::ERROR_SERVER_VERSION_SERVER_NOT_VALIDATE)
                        $appAlert->danger(lng('app.check_update.alert.error_version_server_not_validate', 'url', $errorUrl));
                    else if ($errorServer === AppUpdate::ERROR_SERVER_NOT_FOUND_VERSION_CURRENT_IN_SERVER)
                        $appAlert->danger(lng('app.check_update.alert.error_not_found_version_current_in_server', 'url', $errorUrl));
                    else
                        $appAlert->danger(lng('app.check_update.alert.error_unknown', 'url', $errorUrl));
                }
            }
        } else {
            $updateStatus = $appUpdate->getUpdateStatus();

            if ($updateStatus === AppUpdate::RESULT_VERSION_IS_OLD)
                $appAlert->success(lng('app.check_update.alert.version_is_old', 'version_current', $config->get('version'), 'version_update', $appUpdate->getVersionUpdate()));
            else
                $appAlert->info(lng('app.check_update.alert.version_is_latest', 'version_current', $config->get('version')));
        }
    }
?>

    <?php $appAlert->display(); ?>

    <div class="form-action">
        <div class="title">
            <span><?php echo lng('app.check_update.title_page'); ?></span>
        </div>

        <ul class="about-list">
            <li class="label">
                <ul>
                    <li><span><?php echo lng('app.check_update.info.label.last_check_update'); ?></span></li>
                    <li><span><?php echo lng('app.check_update.info.label.last_upgrade'); ?></span></li>
                    <li><span><?php echo lng('app.check_update.info.label.version_current'); ?></span></li>

                    <?php if (is_array($servers)) { ?>
                        <?php for ($i = 0; $i < count($servers); ++$i) { ?>
                            <li><span><?php echo lng('app.check_update.info.label.server_check', 'index', $i); ?></span></li>
                        <?php } ?>
                    <?php } ?>
                </ul>
            </li>

            <li class="value">
                <ul>
                    <?php if ($config->get('check_at') <= 0) { ?>
                        <li><span><?php echo lng('app.check_update.info.value.not_last_check_update'); ?></span></li>
                    <?php } else { ?>
                        <li><span><?php echo $config->get('check_at'); ?></span></li>
                    <?php } ?>

                    <?php if ($config->get('upgrade_at') <= 0) { ?>
                        <li><span><?php echo lng('app.check_update.info.value.not_last_upgrade'); ?></span></li>
                    <?php } else { ?>
                        <li><span><?php echo $config->get('upgrade_at'); ?></span></li>
                    <?php } ?>

                    <li><span><?php echo $config->get('version'); ?> <?php if ($config->get('is_beta')) echo 'beta'; ?></span></li>

                    <?php if (is_array($servers)) { ?>
                        <?php foreach ($servers AS $server) { ?>
                            <li><span><?php echo $server; ?></span></li>
                        <?php } ?>
                    <?php } ?>
                </ul>
            </li>
        </ul>

        <div class="about-button-check button-action-box center">
            <a href="check_update.php?<?php echo PARAMETER_CHECK_URL; ?>">
                <span><?php echo lng('app.check_update.form.button.check'); ?></span>
            </a>
        </div>
    </div>

    <?php if ($appUser->isLogin()) { ?>
        <ul class="menu-action">
            <li>
                <a href="about.php">
                    <span class="icomoon icon-about"></span>
                    <span><?php echo lng('app.about.menu_action.about'); ?></span>
                </a>
            </li>
            <li>
                <a href="validate_app.php">
                    <span class="icomoon icon-check"></span>
                    <span><?php echo lng('app.about.menu_action.validate_app'); ?></span>
                </a>
            </li>
            <li>
                <a href="help.php">
                    <span class="icomoon icon-help"></span>
                    <span><?php echo lng('app.about.menu_action.help'); ?></span>
                </a>
            </li>
            <li>
                <a href="feedback.php">
                    <span class="icomoon icon-feedback"></span>
                    <span><?php echo lng('app.about.menu_action.feedback'); ?></span>
                </a>
            </li>
        </ul>
    <?php } ?>

<?php require_once(ROOT . 'incfiles' . SP . 'footer.php'); ?>