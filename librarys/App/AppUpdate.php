<?php

    namespace Librarys\App;

    if (defined('LOADED') == false)
        exit;

    use Librarys\File\FileInfo;

    final class AppUpdate
    {

        private $arrayVersions;
        private $versionCurrents;
        private $versionGuestIsOld;
        private $languageGuest;
        private $errorCheck;

        const LANGUAGE_DEFAULT = 'en';

        const PARAMETER_VERSION_GUEST_URL  = 'version_guest';
        const PARAMETER_LANGUAGE_GUEST_URL = 'language_guest';

        const ARRAY_DATA_KEY_VERSION_CURRENT       = 'version_current';
        const ARRAY_DATA_KEY_LISTS                 = 'lists';
        const ARRAY_DATA_KEY_VERSION_VALUE         = 'version';
        const ARRAY_DATA_KEY_VERSION_IS_BETA       = 'is_beta';
        const ARRAY_DATA_KEY_VERSION_CHANGELOG     = 'changelog';
        const ARRAY_DATA_KEY_VERSION_README        = 'readme';
        const ARRAY_DATA_KEY_VERSION_BUILD_LAST    = 'build_last';
        const ARRAY_DATA_KEY_VERSION_DATA_UPGRADE  = 'data_upgrade';
        const ARRAY_DATA_KEY_VERSION_MD5_BIN_CHECK = 'md5_bin_check';
        const ARRAY_DATA_KEY_VERSION_SERVER_NAME   = 'server_name';
        const ARRAY_DATA_KEY_VERSION_PATH          = 'path';
        const ARRAY_DATA_ERROR_INT                 = 'error_int';

        const ERROR_NONE                                      = 0;
        const ERROR_CHECK_NOT_FOUND_LIST_VERSION_IN_SERVER    = 1;
        const ERROR_CHECK_NOT_FOUND_PARAMETER_VERSION_GUEST   = 2;
        const ERROR_CHECK_VERSION_GUEST_NOT_VALIDATE          = 3;
        const ERROR_CHECK_VERSION_SERVER_NOT_VALIDATE         = 4;
        const ERROR_CHECK_NOT_FOUND_VERSION_CURRENT_IN_SERVER = 5;

        const VERSION_BIN_FILENAME       = 'bin.zip';
        const VERSION_BIN_MD5_FILENAME   = 'bin.zip.md5';
        const VERSION_CHANGELOG_FILENAME = 'changelog.md';
        const VERSION_README_FILENAME    = 'readme.md';

        public function __construct($arrayVersions)
        {
            $this->arrayVersions = $arrayVersions;
            $this->errorInt      = self::ERROR_NONE;
        }

        public function checkUpdate()
        {
            if (is_array($this->arrayVersions) == false)
                return $this->errorCheck(self::ERROR_CHECK_NOT_FOUND_LIST_VERSION_IN_SERVER);

            if (isset($_GET[self::PARAMETER_VERSION_GUEST_URL]) == false || empty($_GET[self::PARAMETER_VERSION_GUEST_URL]))
                return $this->errorCheck(self::ERROR_CHECK_NOT_FOUND_PARAMETER_VERSION_GUEST);

            $versionCurrent = $this->arrayVersions[self::ARRAY_DATA_KEY_VERSION_CURRENT];
            $versionGuest   = addslashes(trim($_GET[self::PARAMETER_VERSION_GUEST_URL]));
            $languageGuest  = addslashes(trim(self::LANGUAGE_DEFAULT));

            if (isset($_GET[self::PARAMETER_LANGUAGE_GUEST_URL]) && empty($_GET[self::PARAMETER_LANGUAGE_GUEST_URL]) == false)
                $languageGuest = addslashes(trim($_GET[self::PARAMETER_LANGUAGE_GUEST_URL]));

            if (self::validateVersionValue($versionGuest, $versionGuestMatches) == false)
                return $this->errorCheck(self::ERROR_CHECK_VERSION_GUEST_NOT_VALIDATE);

            if (isset($this->arrayVersions[self::ARRAY_DATA_KEY_LISTS][$versionCurrent]) == false)
                return $this->errorCheck(self::ERROR_CHECK_NOT_FOUND_VERSION_CURRENT_IN_SERVER);

            $versionCurrentData       = $this->arrayVersions[self::ARRAY_DATA_KEY_LISTS][$versionCurrent];
            $versionCurrentInList     = $versionCurrentData[self::ARRAY_DATA_KEY_VERSION_VALUE];
            $pathVersionCurrentInList = $versionCurrentData[self::ARRAY_DATA_KEY_VERSION_PATH];

            if (FileInfo::isTypeDirectory($pathVersionCurrentInList) == false)
                return $this->errorCheck(self::ERROR_CHECK_NOT_FOUND_VERSION_CURRENT_IN_SERVER);

            if (strcasecmp($versionCurrent, $versionCurrentInList) !== 0)
                return $this->errorCheck(self::ERROR_CHECK_VERSION_SERVER_NOT_VALIDATE);

            if (self::validateVersionValue($versionCurrent, $versionCurrentMatches) == false || self::validateVersionValue($versionCurrentInList) == false)
                return $this->errorCheck(self::ERROR_CHECK_VERSION_SERVER_NOT_VALIDATE);

            $pathLanguage = FileInfo::validate($pathVersionCurrentInList . SP . $languageGuest);

            if (FileInfo::isTypeDirectory($pathLanguage))
                $this->languageGuest = $languageGuest;
            else
                $this->languageGuest = self::LANGUAGE_DEFAULT;

            $this->versionGuestIsOld = false;

            if (isset($versionGuestMatches[3]) == false)
                $versionGuestMatches[3] = -1;

            if (isset($versionCurrentMatches[3]) == false)
                $versionCurrentMatches[3] = -1;

            for ($i = 1; $i <= 3; ++$i) {
                if (intval($versionCurrentMatches[$i]) > intval($versionGuestMatches[$i])) {
                    $this->versionGuestIsOld = true;
                    break;
                }
            }

            $this->versionCurrents = $versionCurrentData;
            return true;
        }

        private function errorCheck($errorInt)
        {
            $this->errorCheck = $errorInt;
            return false;
        }

        public function getErrorCheckInt()
        {
            return $this->errorCheck;
        }

        public function responseUpdateResult()
        {
            $versionValue    = $this->versionCurrents[self::ARRAY_DATA_KEY_VERSION_VALUE];
            $isBetaValue     = $this->versionCurrents[self::ARRAY_DATA_KEY_VERSION_IS_BETA];
            $buildLastValue  = $this->versionCurrents[self::ARRAY_DATA_KEY_VERSION_BUILD_LAST];
            $pathValue       = $this->versionCurrents[self::ARRAY_DATA_KEY_VERSION_PATH];

            $dataUpdateValue = null;
            $md5BinValue     = null;
            $changeLogValue  = null;
            $readmeValue     = null;

            if ($this->versionGuestIsOld == false) {
                $changeLogValue = null;
            } else {
                $binFilePath       = FileInfo::validate($pathValue . SP . self::VERSION_BIN_FILENAME);
                $binMd5FilePath    = FileInfo::validate($pathValue . SP . self::VERSION_BIN_MD5_FILENAME);
                $changelogFilePath = FileInfo::validate($pathValue . SP . $this->languageGuest . SP . self::VERSION_CHANGELOG_FILENAME);
                $readmeFilePath    = FileInfo::validate($pathValue . SP . $this->languageGuest . SP . self::VERSION_README_FILENAME);

                if (FileInfo::isTypeFile($changelogFilePath) == false)
                    $changelogFilePath = FileInfo::validate($pathValue . SP . self::LANGUAGE_DEFAULT . SP . self::VERSION_CHANGELOG_FILENAME);

                if (FileInfo::isTypeFile($readmeFilePath) == false)
                    $readmeFilePath = FileInfo::validate($pathValue . SP . self::LANGUAGE_DEFAULT . SP . self::VERSION_README_FILENAME);

                if (FileInfo::fileExists($binFilePath) == false) {
                    $this->errorCheck(self::ERROR_CHECK_NOT_FOUND_VERSION_CURRENT_IN_SERVER);
                } else {
                    if (FileInfo::fileExists($binMd5FilePath) == false)
                        FileInfo::fileWriteContents($binMd5FilePath, @md5_file($binFilePath));

                    $md5BinValue = FileInfo::fileReadContents($binMd5FilePath);

                    if ($md5BinValue === false || empty($md5BinValue))
                        FileInfo::fileWriteContents($binMd5FilePath, @md5_file($binFilePath));

                    if (FileInfo::fileExists($changelogFilePath))
                        $changeLogValue = @bin2hex(FileInfo::fileReadContents($changelogFilePath));

                    if (FileInfo::fileExists($readmeFilePath))
                        $readmeValue = @bin2hex(FileInfo::fileReadContents($readmeFilePath));

                    $md5BinValue     = FileInfo::fileReadContents($binMd5FilePath);
                    $dataUpdateValue = @bin2hex(FileInfo::fileReadContents($binFilePath));
                }
            }

            echo json_encode([
                self::ARRAY_DATA_KEY_VERSION_SERVER_NAME     => $_SERVER['HTTP_HOST'],
                self::ARRAY_DATA_KEY_VERSION_VALUE           => $versionValue,
                self::ARRAY_DATA_KEY_VERSION_IS_BETA         => $isBetaValue,
                self::ARRAY_DATA_KEY_VERSION_BUILD_LAST      => $buildLastValue,
                self::ARRAY_DATA_KEY_VERSION_CHANGELOG       => $changeLogValue,
                self::ARRAY_DATA_KEY_VERSION_README          => $readmeValue,
                self::ARRAY_DATA_KEY_VERSION_DATA_UPGRADE    => $dataUpdateValue,
                self::ARRAY_DATA_KEY_VERSION_MD5_BIN_CHECK   => $md5BinValue,
                self::ARRAY_DATA_ERROR_INT                   => $this->errorCheck
            ]);
        }

        public function responseUpdateError()
        {
            echo json_encode([
                self::ARRAY_DATA_ERROR_INT => $this->errorCheck
            ]);
        }

        public static function validateVersionValue($version, &$matches = null)
        {
            return preg_match('/^(\d)+\.(\d)+\.?(\d)?$/i', $version, $matches);
        }

    }

?>