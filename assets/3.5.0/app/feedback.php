<?php

    define('LOADED', 1);
    require_once('global.php');

    $title  = lng('app.feedback.title_page');
    require_once(ROOT . 'incfiles' . SP . 'header.php');
?>

    <ul class="alert">
        <li class="info">
            <span><?php echo lng('home.alert.features_is_construct'); ?></span>
        </li>
    </ul>

<?php require_once(ROOT . 'incfiles' . SP . 'footer.php'); ?>