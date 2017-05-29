<?php

	namespace Librarys\Firewall;

	use Librarys\Boot;
    use Librarys\File\FileInfo;

	final class FirewallProcess
	{

		private $boot;
		private $ip;
		private $agent;
		private $error;
		private $lastTime;
        private $requestTime;
		private $currentTime;
		private $lockCount;
        private $requestLock;
        private $timeUnlock;

		const ERROR_NONE          = 0;
		const ERROR_IP            = 1;
        const ERROR_LOCK_SMALL    = 2;
        const ERROR_LOCK_MEDIUM   = 3;
        const ERROR_LOCK_LARGE    = 4;
        const ERROR_LOCK_FOREVER  = 5;
        const ERROR_LOCK_HTACCESS = 6;

		public function __construct(Boot $boot)
		{
			$this->boot  = $boot;
			$this->error = self::ERROR_NONE;

			$this->handlerFirewall();
		}

		private function handlerFirewall()
		{
			register_shutdown_function(function() {
				if ($this->isError() && $this->boot->isErrorHandler() == false) {
					$class  = env('app.classes.firewall');

					if ($class != null) {
						$instance = new $class($this->boot);
						$instance->response();
					}
				}
			});
		}

		public function execute()
		{
			$this->ip    = receiverIP();
			$this->agent = receiverUserAgent();

			if ($this->ip == null || empty($this->ip)) {
				$this->error = self::ERROR_IP;

				exit(0);
			}

			$path = env('app.firewall.path');

			if (FileInfo::isTypeDirectory($path) == false)
				trigger_error('Directory firewall not found');

			$file = env('app.firewall.path') . SP . $this->ip;
			$open = null;

			if (FileInfo::isTypeFile($file))
				$open = fopen($file, 'rw+');
			else
				$open = fopen($file, 'w');

			if (FileInfo::isWriteable($file) == false)
				return trigger_error('File "' . $file . '" not writable');

			$size    = filesize($file);
			$content = null;

			if ($size > 0)
				$content = fread($open, $size);

			$ini = parse_ini_string($content);

			$this->lockCount   = 0;
			$this->currentTime = time();
			$this->lastTime    = $this->currentTime;
            $this->requestTime = $this->currentTime;

			if ($ini == false || (is_array($ini) && count($ini) < 0)) {
				$ini = [
					'user_agent'  => $this->agent,
					'lock_count'  => $this->lockCount,
					'last_time'   => $this->currentTime,
                    'request_time' => $this->requestTime
				];
			} else {
                if (is_array($ini) == false)
                    $ini = array();

				if (isset($ini['user_agent']) == false)
					$ini['user_agent'] = $this->agent;

				if (isset($ini['lock_count']) == false)
					$ini['lock_count'] = $this->lockCount;

				if (isset($ini['last_time']) == false)
					$ini['last_time'] = $this->currentTime;

                if (isset($ini['request_time']) == false)
                    $ini['request_time'] = $this->currentTime;
			}

			$this->lastTime    = intval(stripslashes($ini['last_time']));
			$this->lockCount   = intval(stripslashes($ini['lock_count']));
            $this->requestTime = intval(stripslashes($ini['request_time']));

            if ($this->currentTime - $this->requestTime < env('app.firewall.time.request')) {
                $this->lockCount++;

                $this->lastTime    = $this->currentTime;
                $this->requestLock = true;
            } else {
                $this->requestLock = false;
            }

            if ($this->lockCount >= env('app.firewall.lock_count.htaccess') && env('app.firewall.enable_htaccess') && FileInfo::isTypeFile(env('app.firewall.path_htaccess'))) {
                $this->error = self::ERROR_LOCK_HTACCESS;

                if (($fopen = fopen(env('app.firewall.path_htaccess'), 'rw+')) != false) {
                    $content = fread($fopen, filesize(env('app.firewall.path_htaccess')));

                    if ($content !== false) {
                        if (preg_match('/Deny from \d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/si', $content) == false)
                            $content .= "\n" . 'Deny from ' . $this->ip;

                        rewind($fopen);
                        fflush($fopen);
                        fwrite($fopen, $content);
                        fclose($fopen);
                    }
                }
            } else {
                $isWrite = true;

                if ($this->lockCount > env('app.firewall.lock_count.forever')) {
                    $this->error = self::ERROR_LOCK_FOREVER;

                    if (env('app.firewall.enable_htaccess') == false)
                        $isWrite = false;
                } else {
                    if ($this->lockCount > env('app.firewall.lock_count.large')) {
                        $this->error = self::ERROR_LOCK_LARGE;
                        $this->timeUnlock  = env('app.firewall.time.large');
                    } else if ($this->lockCount > env('app.firewall.lock_count.medium')) {
                        $this->error = self::ERROR_LOCK_MEDIUM;
                        $this->timeUnlock  = env('app.firewall.time.medium');
                    } else if ($this->lockCount > env('app.firewall.lock_count.small')) {
                        $this->error = self::ERROR_LOCK_SMALL;
                        $this->timeUnlock  = env('app.firewall.time.small');
                    }

                    if ($this->requestLock == false && $this->timeUnlock > 0) {
                        if ($this->currentTime - $this->lastTime >= $this->timeUnlock) {
                            $this->lockCount   = 0;
                            $this->requestLock = true;
                            $this->error       = self::ERROR_NONE;
                        }
                    }
                }

                if ($isWrite && $this->requestLock) {
                    rewind($open);
                    fflush($open);

                    fwrite($open, 'request_time = "' . addslashes($this->currentTime) . '";' . "\n");
                    fwrite($open, 'last_time    = "' . addslashes($this->currentTime) . '";' . "\n");
                    fwrite($open, 'lock_count   = "' . addslashes($this->lockCount)   . '";' . "\n");
                    fwrite($open, 'user_agent   = "' . addslashes($this->agent)       . '";' . "\n");
                    fflush($open);

                    if ($this->lockCount <= 0 && $this->requestLock)
                        $this->requestLock = false;
                } else if ($isWrite) {
                    rewind($open);
                    fflush($open);

                    fwrite($open, 'request_time = "' . addslashes($this->currentTime) . '";' . "\n");
                    fwrite($open, 'last_time    = "' . addslashes($this->lastTime)    . '";' . "\n");
                    fwrite($open, 'lock_count   = "' . addslashes($this->lockCount)   . '";' . "\n");
                    fwrite($open, 'user_agent   = "' . addslashes($this->agent)       . '";' . "\n");
                    fflush($open);
                }
            }

            if (is_resource($open))
                fclose($open);

            if ($this->isError())
                exit(0);
		}

        public function getIP()
        {
            return $this->ip;
        }

        public function getUserAgent()
        {
            return $this->agent;
        }

        public function getError()
        {
            return $this->error;
        }

        public function getLastTime()
        {
            return $this->lastTime;
        }

        public function getRequestTime()
        {
            return $this->requestTime;
        }

        public function getCurrentTime()
        {
            return $this->currentTime;
        }

        public function getLockCount()
        {
            return $this->lockCount;
        }

        public function getRequestLock()
        {
            return $this->requestLock;
        }

        public function getTimeUnlock()
        {
            return $this->timeUnlock;
        }

		public function isError()
		{
			return $this->error != self::ERROR_NONE;
		}

	}

?>