<?php

    namespace Librarys\App\Base;

    use Librarys\Boot;
    use Librarys\App\AppUser;
    use Librarys\File\FileInfo;

    abstract class BaseConfigRead
    {

        protected $boot;

        protected $pathConfig;
        protected $pathConfigSystem;

        protected $fileConfigName;

        protected $configArray;
        protected $configSystemArray;

        protected $cacheArray;
        protected $envProtectedArray;

        public function __construct(Boot $boot, $pathConfigSystem, $fileConfigName)
        {
            $this->configArray       = array();
            $this->configSystemArray = array();
            $this->cacheArray        = array();

            $this->setPathConfigSystem($pathConfigSystem);
            $this->setFileConfigName($fileConfigName);
        }

        public function getPathConfig()
        {
            return $this->pathConfig;
        }

        public function setPathConfigSystem($path)
        {
            if ($path == null)
                return false;
            else
               $this->pathConfigSystem = $path;

            return true;
        }

        public function getPathConfigSystem()
        {
            return $this->pathConfigSystem;
        }

        public function setFileConfigName($fileConfigName)
        {
            if ($fileConfigName == null)
                return false;
            else
                $this->fileConfigName = $fileConfigName;

            return true;
        }

        public function getFileConfigName()
        {
            return $this->fileConfigName;
        }

        public function getConfigArray()
        {
            return $this->configArray;
        }

        public function getConfigArraySystem()
        {
            return $this->configSystemArray;
        }

        public function execute(AppUser $appUser)
        {
            if ($appUser->isLogin()) {
                $username  = $appUser->get('username');
                $directory = env('app.path.user');
                $isMkdir   = true;

                $this->pathConfig = FileInfo::validate($directory . SP . md5($username));

                if (FileInfo::isTypeDirectory($directory) == false)
                    $isMkdir = FileInfo::mkdir($directory);

                if ($isMkdir) {
                    if (FileInfo::isTypeDirectory($this->pathConfig) == false)
                        $isMkdir = FileInfo::mkdir($this->pathConfig);

                    if ($isMkdir) {
                        $this->pathConfig = FileInfo::validate($this->pathConfig . SP . $this->fileConfigName);

                        if (FileInfo::isTypeFile($this->pathConfig))
                            return $this->parse();
                    }
                }
            } else {
                $this->pathConfig = $this->pathConfigSystem;
            }

            $this->parse();
        }

        public function parse($parseSystem = false)
        {
            $path = $this->pathConfig;

            if ($parseSystem == false) {
                if (is_null($path) || FileInfo::isTypeFile($path) == false)
                    $path = $this->pathConfigSystem;

                if (FileInfo::isTypeFile($path) == false)
                    return;
            } else {
                $path = $this->pathConfigSystem;

                if (is_null($path) && FileInfo::isTypeFile($path) == false)
                    return;
            }

            if ($parseSystem)
                $this->configSystemArray = require($path);
            else
                $this->configArray = require($path);

            if (is_array($this->configSystemArray) == false)
                $this->configSystemArray = array();

            if (is_array($this->configArray) == false)
                $this->configArray = array();
        }

        public function set($name, $value)
        {
            if ($name == null)
                return false;

            $nameSplits = array();

            if (strpos($name, '.') === false)
                $nameSplits[] = $name;
            else
                $nameSplits = explode('.', $name);

            $configArray     = &$this->configArray;
            $nameSplitsCount = count($nameSplits);

            for ($i = 0; $i < $nameSplitsCount; ++$i) {
                $nameEntry = $nameSplits[$i];

                if ($i === $nameSplitsCount - 1) {
                    $configArray[$nameEntry] = $value;
                } else {
                    if (isset($configArray[$nameEntry]) == false)
                        $configArray[$nameEntry] = [];

                    $configArray = &$configArray[$nameEntry];
                }
            }

            if (array_key_exists($name, $this->cacheArray) == true)
                $this->receiverToCache($name);

            return true;
        }

        public function get($name, $default = null, $recache = false)
        {
            if (array_key_exists($name, $this->cacheArray) && $recache == false)
                return $this->cacheArray[$name];

            return $this->receiverToCache($name, $default);
        }

        private function receiverToCache($name, $default = null)
        {
            return ($this->cacheArray[$name] = urlSeparatorMatches($this->receiver($name, $default)));
        }

        private function receiver($key, $default = null, $array = null)
        {
            if (is_string($key) && empty($key) == false) {
                // Config of user
                if ($array == null)
                    $array = $this->configArray;

                if (is_array($array) == false)
                    return null;

                $keys  = explode('.', $key);

                if (is_array($keys) == false)
                    $keys = array($key);

                foreach ($keys AS $entry) {
                    $entry = trim($entry);

                    if (array_key_exists($entry, $array) == false) {
                        $array = null;
                        break;
                    } else {
                        $array = $array[$entry];
                    }
                }

                if ($array === null) {
                    // Config of system
                    $array = $this->configSystemArray;

                    if (is_array($array) == false)
                        return null;

                    foreach ($keys AS $entry) {
                        $entry = trim($entry);

                        if (array_key_exists($entry, $array) == false)
                            return $default;

                        $array = $array[$entry];
                    }
                }

                return $this->envMatchesString($array);
            }

            return $default;
        }

        public function envMatchesString($str)
        {
            if (is_array($str) || preg_match('/\$\{(.+?)\}/si', $str, $matches) == false)
                return $str;

            return preg_replace_callback('/\$\{(.+?)\}/si', function($matches) {
                $result = null;

                if (isset($GLOBALS[$matches[1]]))
                    $result = $GLOBALS[$matches[1]];
                else if (defined($matches[1]))
                    $result = constant($matches[1]);
                else
                    $result = env(trim($matches[1]));

                if (is_array($result))
                    return 'Array';
                else if (is_object($result))
                    return 'Object';
                else if (is_resource($result))
                    return 'Resource';

                return $result;
            }, $str);
        }

        public function requireEnvProtected($path)
        {
            if (FileInfo::isTypeFile($path) == false)
                return false;

            $envProtecteds = require($path);

            if (is_array($envProtecteds) == false)
                return false;

            foreach ($envProtecteds AS $envKey => $envFlag) {
                if ($envFlag)
                    $this->addEnvProtected($envKey, true);
            }
        }

        public function addEnvProtected($name, $disabledCreateToUser = true)
        {
            if (is_array($this->envProtectedArray) == false)
                $this->envProtectedArray = array();

            $this->envProtectedArray[$name] = $disabledCreateToUser;
        }

        public function isEnvEnabled($name)
        {
            return $this->isEnvDisabled($name) == false;
        }

        public function isEnvDisabled($name)
        {
            if (is_array($this->envProtectedArray) && array_key_exists($name, $this->envProtectedArray))
                return $this->envProtectedArray[$name] == true;

            return false;
        }

    }

?>