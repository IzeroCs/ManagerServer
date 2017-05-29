<?php

    namespace Librarys\App;

    if (defined('LOADED') == false)
        exit;

    final class AppUpdate
    {

        private $arrayVersions;
        private $versionCurrents;
        private $versionGuestIsOld;
        private $errorCheck;

        const PARAMETER_VERSION_GUEST_URL = 'version_guest';

        const ARRAY_DATA_KEY_VERSION_CURRENT         = 'version_current';
        const ARRAY_DATA_KEY_LISTS                   = 'lists';
        const ARRAY_DATA_KEY_VERSION_VALUE           = 'version';
        const ARRAY_DATA_KEY_VERSION_IS_BETA         = 'is_beta';
        const ARRAY_DATA_KEY_VERSION_CHANGELOG       = 'changelog';
        const ARRAY_DATA_KEY_VERSION_BUILD_LAST      = 'build_last';
        const ARRAY_DATA_KEY_VERSION_COMPRESS_METHOD = 'compress_method';
        const ARRAY_DATA_KEY_VERSION_PATH            = 'path';
        const ARRAY_DATA_ERROR_INT                   = 'error_int';

        const ERROR_NONE                                      = 0;
        const ERROR_CHECK_NOT_FOUND_LIST_VERSION_IN_SERVER    = 1;
        const ERROR_CHECK_NOT_FOUND_PARAMETER_VERSION_GUEST   = 2;
        const ERROR_CHECK_VERSION_GUEST_NOT_VALIDATE          = 3;
        const ERROR_CHECK_VERSION_SERVER_NOT_VALIDATE         = 4;
        const ERROR_CHECK_NOT_FOUND_VERSION_CURRENT_IN_SERVER = 5;

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

            if (self::validateVersionValue($versionGuest, $versionGuestMatches) == false)
                return $this->errorCheck(self::ERROR_CHECK_VERSION_GUEST_NOT_VALIDATE);

            if (isset($this->arrayVersions[self::ARRAY_DATA_KEY_LISTS][$versionCurrent]) == false)
                return $this->errorCheck(self::ERROR_CHECK_NOT_FOUND_VERSION_CURRENT_IN_SERVER);

            $versionCurrentData       = $this->arrayVersions[self::ARRAY_DATA_KEY_LISTS][$versionCurrent];
            $versionCurrentInList     = $versionCurrentData[self::ARRAY_DATA_KEY_VERSION_VALUE];
            $pathVersionCurrentInList = $versionCurrentData[self::ARRAY_DATA_KEY_VERSION_PATH];

            if (@is_dir($pathVersionCurrentInList) == false)
                return $this->errorCheck(self::ERROR_CHECK_NOT_FOUND_VERSION_CURRENT_IN_SERVER);

            if (strcasecmp($versionCurrent, $versionCurrentInList) !== 0)
                return $this->errorCheck(self::ERROR_CHECK_VERSION_SERVER_NOT_VALIDATE);

            if (self::validateVersionValue($versionGuest, $versionCurrentMatches) == false || self::validateVersionValue($versionCurrentInList) == false)
                return $this->errorCheck(self::ERROR_CHECK_VERSION_SERVER_NOT_VALIDATE);

            $this->versionGuestIsOld = false;

            if (isset($versionGuestMatches[3]) == false)
                $versionGuestMatches[3] = -1;

            for ($i = 0; $i < 3; ++$i) {
                if ($versionCurrentMatches[$i] > $versionGuestMatches[$i]) {
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
            $versionValue        = $this->versionCurrents[self::ARRAY_DATA_KEY_VERSION_VALUE];
            $isBetaValue         = $this->versionCurrents[self::ARRAY_DATA_KEY_VERSION_IS_BETA];
            $buildLastValue      = $this->versionCurrents[self::ARRAY_DATA_KEY_VERSION_BUILD_LAST];
            $changeLogValue      = $this->versionCurrents[self::ARRAY_DATA_KEY_VERSION_CHANGELOG];
            $compressMethodValue = $this->versionCurrents[self::ARRAY_DATA_KEY_VERSION_COMPRESS_METHOD];
            $pathValue           = $this->versionCurrents[self::ARRAY_DATA_KEY_VERSION_PATH];
            $dataUpdateValue     = null;

            if ($this->versionGuestIsOld == false)
                $changeLogValue = null;

            echo json_encode([
                self::ARRAY_DATA_KEY_VERSION_VALUE           => $versionValue,
                self::ARRAY_DATA_KEY_VERSION_IS_BETA         => $isBetaValue,
                self::ARRAY_DATA_KEY_VERSION_BUILD_LAST      => $buildLastValue,
                self::ARRAY_DATA_KEY_VERSION_CHANGELOG       => $changeLogValue,
                self::ARRAY_DATA_KEY_VERSION_COMPRESS_METHOD => $compressMethodValue
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