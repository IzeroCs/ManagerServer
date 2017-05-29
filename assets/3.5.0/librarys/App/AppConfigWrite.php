<?php

	namespace Librarys\App;

    use Librarys\App\Base\BaseConfigWrite;

	final class AppConfigWrite extends BaseConfigWrite
	{

		public function __construct(AppConfig $appConfig)
		{
            parent::__construct($appConfig, $appConfig->getPathConfig());
		}

        public function callbackPreWrite()
        {
            if ($this->baseConfigRead->getPathConfig() == $this->baseConfigRead->getPathConfigSystem())
                return false;

            return true;
        }

        public function resultConfigArray()
        {
            return $this->baseConfigRead->getConfigArray();
        }

	}

?>