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
        private $buildGuest;
        private $languageGuest;
        private $errorCheck;

        const LANGUAGE_DEFAULT = 'en';

        const PARAMETER_VERSION_GUEST_URL  = 'version_guest';
        const PARAMETER_VERSION_BUILD_URL  = 'version_build_guest';
        const PARAMETER_LANGUAGE_GUEST_URL = 'language_guest';

        const ARRAY_DATA_KEY_VERSION_CURRENT              = 'version_current';
        const ARRAY_DATA_KEY_LISTS                        = 'lists';
        const ARRAY_DATA_KEY_VERSION_VALUE                = 'version';
        const ARRAY_DATA_KEY_VERSION_IS_BETA              = 'is_beta';
        const ARRAY_DATA_KEY_VERSION_CHANGELOG            = 'changelog';
        const ARRAY_DATA_KEY_VERSION_README               = 'readme';
        const ARRAY_DATA_KEY_VERSION_BUILD_LAST           = 'build_last';
        const ARRAY_DATA_KEY_VERSION_DATA_UPGRADE         = 'data_upgrade';
        const ARRAY_DATA_KEY_VERSION_ADDITIONAL_UPDATE    = 'additional_update';
        const ARRAY_DATA_KEY_VERSION_UPDATE_SCRIPT        = 'update_script';
        const ARRAY_DATA_KEY_VERSION_MD5_BIN_CHECK        = 'md5_bin_check';
        const ARRAY_DATA_KEY_VERSION_MD5_ADDITIONAL_CHECK = 'md5_additional_check';
        const ARRAY_DATA_KEY_VERSION_SERVER_NAME          = 'server_name';
        const ARRAY_DATA_KEY_VERSION_PATH                 = 'path';
        const ARRAY_DATA_KEY_VERSION_ENTRY_IGONE_REMOVE   = 'entry_igone_remove';
        const ARRAY_DATA_ERROR_INT                        = 'error_int';

        const ERROR_NONE                                      = 0;
        const ERROR_CHECK_NOT_FOUND_LIST_VERSION_IN_SERVER    = 1;
        const ERROR_CHECK_NOT_FOUND_PARAMETER_VERSION_GUEST   = 2;
        const ERROR_CHECK_NOT_FOUND_PARAMETER_BUILD_GUEST     = 3;
        const ERROR_CHECK_VERSION_GUEST_NOT_VALIDATE          = 4;
        const ERROR_CHECK_VERSION_SERVER_NOT_VALIDATE         = 5;
        const ERROR_CHECK_NOT_FOUND_VERSION_CURRENT_IN_SERVER = 6;

        const VERSION_BIN_FILENAME            = 'bin.zip';
        const VERSION_ADDITIONAL_FILENAME     = 'additional.zip';
        const VERSION_BIN_MD5_FILENAME        = 'bin.zip.md5';
        const VERSION_ADDITIONAL_MD5_FILENAME = 'additional.zip.md5';
        const VERSION_CHANGELOG_FILENAME      = 'changelog.md';
        const VERSION_README_FILENAME         = 'readme.md';
        const VERSION_UPDATE_SCRIPT_FILENAME  = 'update.script';

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

            if (isset($_GET[self::PARAMETER_VERSION_BUILD_URL]) == false || empty($_GET[self::PARAMETER_VERSION_BUILD_URL]))
                return $this->errorCheck(self::ERROR_CHECK_NOT_FOUND_PARAMETER_BUILD_GUEST);

            $versionCurrent = $this->arrayVersions[self::ARRAY_DATA_KEY_VERSION_CURRENT];
            $versionGuest   = addslashes(trim($_GET[self::PARAMETER_VERSION_GUEST_URL]));
            $languageGuest  = addslashes(trim(self::LANGUAGE_DEFAULT));
            $buildGuest     = intval(addslashes($_GET[self::PARAMETER_VERSION_BUILD_URL]));

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

            $pathLanguage = FileInfo::filterPaths($pathVersionCurrentInList . SP . $languageGuest);

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
            $this->buildGuest      = $buildGuest;

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
            $versionValue          = $this->versionCurrents[self::ARRAY_DATA_KEY_VERSION_VALUE];
            $isBetaValue           = $this->versionCurrents[self::ARRAY_DATA_KEY_VERSION_IS_BETA];
            $entryIgoneRemoveValue = $this->versionCurrents[self::ARRAY_DATA_KEY_VERSION_ENTRY_IGONE_REMOVE];
            $pathValue             = $this->versionCurrents[self::ARRAY_DATA_KEY_VERSION_PATH];

            $dataUpdateValue       = null;
            $additionalUpdateValue = null;
            $md5BinValue           = null;
            $md5AdditionalValue    = null;
            $changeLogValue        = null;
            $readmeValue           = null;
            $updateScriptValue     = null;
            $buildLastValue        = null;

            $binFilePath         = FileInfo::filterPaths($pathValue . SP . self::VERSION_BIN_FILENAME);
            $additionalFilePath  = FileInfo::filterPaths($pathValue . SP . self::VERSION_ADDITIONAL_FILENAME);
            $additionalBuild     = 0;
            $hasAdditionalUpdate = false;

            if ($this->versionGuestIsOld && FileInfo::fileExists($binFilePath)) {
                $buildLastValue = FileInfo::fileMTime($binFilePath);
            } else if (FileInfo::fileExists($additionalFilePath)) {
                $additionalBuild     = FileInfo::fileMTime($additionalFilePath);
                $hasAdditionalUpdate = $additionalBuild > $this->buildGuest;

                if ($hasAdditionalUpdate)
                    $buildLastValue = $additionalBuild;
            }

            if ($this->versionGuestIsOld == false && $hasAdditionalUpdate == false) {
                $changeLogValue = null;
            } else {
                $binMd5FilePath        = FileInfo::filterPaths($pathValue . SP . self::VERSION_BIN_MD5_FILENAME);
                $additionalMd5FilePath = FileInfo::filterPaths($pathValue . SP . self::VERSION_ADDITIONAL_MD5_FILENAME);
                $changelogFilePath     = FileInfo::filterPaths($pathValue . SP . $this->languageGuest . SP . self::VERSION_CHANGELOG_FILENAME);
                $readmeFilePath        = FileInfo::filterPaths($pathValue . SP . $this->languageGuest . SP . self::VERSION_README_FILENAME);
                $updateScriptPath      = FileInfo::filterPaths($pathValue . SP . self::VERSION_UPDATE_SCRIPT_FILENAME);

                if (FileInfo::isTypeFile($changelogFilePath) == false)
                    $changelogFilePath = FileInfo::filterPaths($pathValue . SP . self::LANGUAGE_DEFAULT . SP . self::VERSION_CHANGELOG_FILENAME);

                if (FileInfo::isTypeFile($readmeFilePath) == false)
                    $readmeFilePath = FileInfo::filterPaths($pathValue . SP . self::LANGUAGE_DEFAULT . SP . self::VERSION_README_FILENAME);

                if (FileInfo::fileExists($binFilePath) == false && $this->versionGuestIsOld) {
                    $this->errorCheck(self::ERROR_CHECK_NOT_FOUND_VERSION_CURRENT_IN_SERVER);
                } else {
                    FileInfo::fileWriteContents($binMd5FilePath, @md5_file($binFilePath));

                    if ($hasAdditionalUpdate)
                        FileInfo::fileWriteContents($additionalMd5FilePath, @md5_file($additionalFilePath));

                    if (FileInfo::fileExists($changelogFilePath))
                        $changeLogValue = @bin2hex(FileInfo::fileReadContents($changelogFilePath));

                    if (FileInfo::fileExists($readmeFilePath))
                        $readmeValue = @bin2hex(FileInfo::fileReadContents($readmeFilePath));

                    if (FileInfo::fileExists($updateScriptPath))
                        $updateScriptValue = @bin2hex(FileInfo::fileReadContents($updateScriptPath));

                    if ($this->versionGuestIsOld) {
                        $md5BinValue     = FileInfo::fileReadContents($binMd5FilePath);
                        $dataUpdateValue = @bin2hex(FileInfo::fileReadContents($binFilePath));
                    } else if ($hasAdditionalUpdate) {
                        $md5AdditionalValue    = FileInfo::fileReadContents($additionalMd5FilePath);
                        $additionalUpdateValue = @bin2hex(FileInfo::fileReadContents($additionalFilePath));
                    }
                }
            }

            echo json_encode([
                self::ARRAY_DATA_KEY_VERSION_SERVER_NAME          => $_SERVER['HTTP_HOST'],
                self::ARRAY_DATA_KEY_VERSION_VALUE                => $versionValue,
                self::ARRAY_DATA_KEY_VERSION_IS_BETA              => $isBetaValue,
                self::ARRAY_DATA_KEY_VERSION_BUILD_LAST           => $buildLastValue,
                self::ARRAY_DATA_KEY_VERSION_CHANGELOG            => $changeLogValue,
                self::ARRAY_DATA_KEY_VERSION_README               => $readmeValue,
                self::ARRAY_DATA_KEY_VERSION_DATA_UPGRADE         => $dataUpdateValue,
                self::ARRAY_DATA_KEY_VERSION_ADDITIONAL_UPDATE    => $additionalUpdateValue,
                self::ARRAY_DATA_KEY_VERSION_MD5_BIN_CHECK        => $md5BinValue,
                self::ARRAY_DATA_KEY_VERSION_MD5_ADDITIONAL_CHECK => $md5AdditionalValue,
                self::ARRAY_DATA_KEY_VERSION_UPDATE_SCRIPT        => $updateScriptValue,
                self::ARRAY_DATA_KEY_VERSION_ENTRY_IGONE_REMOVE   => $entryIgoneRemoveValue,
                self::ARRAY_DATA_ERROR_INT                        => $this->errorCheck
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