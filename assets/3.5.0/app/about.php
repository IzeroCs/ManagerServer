<?php

    use Librarys\App\AppAboutConfig;

    define('LOADED', 1);
    require_once('global.php');

    $title  = lng('app.about.title_page');
    $themes = [ env('resource.theme.about') ];
    $config = new AppAboutConfig($boot, env('resource.config.about'));

    require_once(ROOT . 'incfiles' . SP . 'header.php');
?>

    <div id="about">
        <h1><?php echo $config->get('name'); ?></h1>
        <ul>
        	<li class="label">
        		<ul>
        			<li><span><?php echo lng('app.about.info.label.author'); ?></span></li>
        			<li><span><?php echo lng('app.about.info.label.version'); ?></span></li>
        			<li><span><?php echo lng('app.about.info.label.email'); ?></span></li>
        			<li><span><?php echo lng('app.about.info.label.github'); ?></span></li>
        			<li><span><?php echo lng('app.about.info.label.facebook'); ?></span></li>
        			<li><span><?php echo lng('app.about.info.label.phone'); ?></span></li>
        			<li><span><?php echo lng('app.about.info.label.create_at'); ?></span></li>
                    <li><span><?php echo lng('app.about.info.label.upgrade_at'); ?></span></li>
        			<li><span><?php echo lng('app.about.info.label.check_at'); ?></span></li>
        		</ul>
        	</li>
        	<li class="value">
        		<ul>
        			<li><span><?php echo $config->get('author'); ?></span></li>
        			<li><span><?php echo $config->get('version'); ?> <?php if ($config->get('is_beta')) echo 'beta'; ?></span></li>
        			<li><span><?php echo $config->get('email'); ?></span></li>
        			<li><span><a href="<?php echo $config->get('git_link'); ?>" target="_blank"><?php echo $config->get('git_title'); ?></a></span></li>
        			<li><span><a href="<?php echo $config->get('fb_link'); ?>" target="_blank"><?php echo $config->get('fb_title'); ?></a></span></li>
        			<li><span><?php echo $config->get('phone'); ?></span></li>
        			<li><span><?php echo date('d.m.Y - H:i', $config->get('create_at')); ?></span></li>
                    <li><span><?php echo date('d.m.Y - H:i', $config->get('update_at')); ?></span></li>
        			<li><span><?php echo date('d.m.Y - H:i', $config->get('check_at')); ?></span></li>
        		</ul>
        	</li>
        </ul>
    </div>

    <?php if ($appUser->isLogin()) { ?>
        <ul class="menu-action">
            <li>
                <a href="check_update.php">
                    <span class="icomoon icon-update"></span>
                    <span><?php echo lng('app.about.menu_action.check_update'); ?></span>
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