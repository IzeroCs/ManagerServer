<?php

    namespace Librarys\App\Base;

    use Librarys\File\FileInfo;

    abstract class BaseConfigWrite
    {

        protected $baseConfigRead;

        protected $pathConfig;
        protected $configArray;
        protected $buffer;
        protected $spacing;

        public function __construct(BaseConfigRead $baseConfigRead, $pathConfig = null)
        {
            $this->baseConfigRead = $baseConfigRead;
            $this->setPathConfig($pathConfig);
        }

        public function getBaseConfigRead()
        {
            return $this->baseConfigRead;
        }

        public function setPathConfig($path)
        {
            $this->pathConfig = $path;
        }

        public function getPathConfig()
        {
            return $this->pathConfig;
        }

        public function setConfigArray(array $configArray)
        {
            $this->configArray = $configArray;
        }

        public function getConfigArray()
        {
            return $this->configArray;
        }

        public function setSpacing($spacing)
        {
            $this->spacing = $spacing;
        }

        public function getSpacing()
        {
            return $this->spacing;
        }

        public abstract function callbackPreWrite();

        public abstract function resultConfigArray();

        public function write()
        {
            if ($this->baseConfigRead == null)
                return false;

            if ($this->callbackPreWrite() == false)
                return false;

            $config = $this->resultConfigArray();

            if (is_array($config)) {
                $this->sortArray($config);

                $this->buffer  = '<?php' . "\n\n";
                $this->buffer .= $this->spacing . 'return [' . "\n";

                foreach ($config AS $key => $entry)
                    $this->writeBufferEntry($key, $entry, $this->spacing);

                $this->buffer .= $this->spacing . '];' . "\n\n";
                $this->buffer .= '?>';

                if ($this->pathConfig == null)
                    return false;

                if (FileInfo::fileWriteContents($this->pathConfig, $this->buffer))
                    return true;
            }

            return false;
        }

        protected function writeBufferEntry($key, &$entry, $spacing = null, $envKey = null)
        {
            $spacing .= '    ';

            if ($envKey == null)
                $envKey = $key;
            else
                $envKey .= '.' . $key;

            if ($this->baseConfigRead->isEnvDisabled($envKey))
                return;

            if (is_array($entry)) {
                $this->sortArray($entry);
                $this->buffer .= $spacing . '\'' . $key . '\' => [' . "\n";

                foreach ($entry AS $keyWith => $entryWith)
                    $this->writeBufferEntry($keyWith, $entryWith, $spacing, $envKey);

                $this->buffer .= $spacing . '],' . "\n\n";
            } else {
                $type = null;

                if ($entry != null)
                    $type = getType($entry);

                if ($type == null && is_numeric($entry)) {
                    if (is_int($entry))
                        $type = 'integer';
                    else if (is_float($entry))
                        $type = 'float';
                    else if (is_double($entry))
                        $type = 'double';
                }

                $this->buffer .= $spacing . '\'' . $key . '\' => ';

                if ($type == 'string') {
                    $this->buffer .= '\'' . $entry . '\'';
                } else if ($type == 'integer') {
                    $this->buffer .= intval($entry);
                } else if ($type == 'float') {
                    $this->buffer .= floatval($entry);
                } else if ($type == 'double') {
                    $this->buffer .= doubleval($entry);
                } else if ($type == 'boolean' || $entry === false) {
                    if (boolval($entry) == true)
                        $this->buffer .= 'true';
                    else
                        $this->buffer .= 'false';
                } else if ($type === null) {
                    $this->buffer .= '\'\'';
                } else {
                    $this->buffer .= $entry;
                }

                $this->buffer .= ",\n";
            }
        }

        protected function sortArray($array)
        {
            if (is_array($array))
                krsort($array);
        }

    }

?>