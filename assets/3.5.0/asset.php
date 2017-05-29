<?php

    use Librarys\File\FileInfo;

    define('LOADED', 1);
    require_once('incfiles' . DIRECTORY_SEPARATOR . 'global.php');
    requireDefine('asset');

    if ($boot->getCFSRToken()->validateGet() !== true) {
        die('CFSR Token không hợp lệ');
        exit(0);
    }

    $cachePath     = env('app.path.cache');
    $cacheLifetime = 0;

    function checkCache($path, $lifetime)
    {
        if (FileInfo::fileExists($path) == false)
            return false;

        if ($lifetime <= 0 || time() - FileInfo::fileMTime($path) > $lifetime)
            return false;

        return true;
    }

    if (
            isset($_GET[ASSET_PARAMETER_THEME_URL]) && empty($_GET[ASSET_PARAMETER_THEME_URL]) == false &&
            isset($_GET[ASSET_PARAMETER_CSS_URL])   && empty($_GET[ASSET_PARAMETER_CSS_URL]) == false
    ) {
        $themeDirectory = addslashes(trim($_GET[ASSET_PARAMETER_THEME_URL]));
        $themeFile      = addslashes(trim($_GET[ASSET_PARAMETER_CSS_URL]));
        $themePath      = env('app.path.theme');
        $themeEntrys    = array();

        $cachePath    .= $themeFile . '.css';
        $cacheLifetime = env('app.dev.cache_css');

        if (checkCache($cachePath, $cacheLifetime) == false) {
            if (strpos($themeFile, '.'))
                $themeEntrys = explode('.', $themeFile);
            else
                array_push($themeEntrys, $themeFile);

            if (FileInfo::isTypeDirectory(FileInfo::validate($themePath . SP . $themeDirectory)) == false)
                die('Thư mục chứa tài nguyên không tồn tại');

            header('Content-Type: text/css');

            foreach ($themeEntrys AS $themeFilename) {
                $themeFilepath = FileInfo::validate($themePath . SP . $themeDirectory . SP . $themeFilename . '.css');

                if (FileInfo::isTypeFile($themeFilepath)) {
                    require $themeFilepath;
                    echo "\n\n";
                }
            }
        }
    } else if (isset($_GET[ASSET_PARAMETER_JS_URL]) && empty($_GET[ASSET_PARAMETER_JS_URL]) == false) {
        $jsFile   = addslashes(trim($_GET[ASSET_PARAMETER_JS_URL]));
        $jsPath   = env('app.path.javascript');
        $jsEntrys = array();

        $cachePath    .= $jsFile . '.js';
        $cacheLifetime = env('app.dev.cache_js');

        if (checkCache($cachePath, $cacheLifetime) == false) {
            if (strpos($jsFile, '.'))
                $jsEntrys = explode('.', $jsFile);
            else
                array_push($jsEntrys, $jsFile);

            header('Content-Type: text/javascript');

            foreach ($jsEntrys AS $jsFilename) {
                $jsFilepath = FileInfo::validate($jsPath . SP . $jsFilename . '.js');

                if (FileInfo::isTypeFile($jsFilepath)) {
                    require $jsFilepath;
                    echo "\n\n";
                }
            }
        }
    } else {
        die('Tài nguyên không hợp lệ');
        exit(0);
    }

    if ($cacheLifetime > 0) {
        $contents = @ob_get_contents();

        $boot->obBufferClean();
        $boot->obBufferStart();

        echo ($contents);
    }
?>