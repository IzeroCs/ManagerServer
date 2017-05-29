<?php

    namespace Librarys;

    if (defined('SP') == false)
        define('SP', DIRECTORY_SEPARATOR);

    error_reporting(E_ALL);

    require_once(__DIR__ . SP . 'Function.php');
    require_once(__DIR__ . SP . 'Environment.php');
    require_once(__DIR__ . SP . 'Autoload.php');
    require_once(__DIR__ . SP . 'CFSR' . SP . 'CFSRToken.php');

    use Librarys\Autoload;
    use Librarys\CFSR\CFSRToken;
    use Librarys\Firewall\FirewallProcess;

    final class Boot
    {

        private $environment;
        private $language;
        private $firewall;
        private $autoload;
        private $cfsr;

        private $isErrorHandler;

        public function __construct(array $config)
        {
            $this->obBufferStart();
            $this->obBufferEnd();
            $this->fixMagicQuotesGpc();

            $this->environment = new Environment($config);
            $this->language    = new Language($this);
            $this->autoload    = new Autoload($this);

            $this->environment->execute();

            $this->obErrorHandler();
            $this->dateInitializing();

            $this->language->execute();
            $this->autoload->execute();

            if (env('app.session.init', false) == true)
                $this->sessionInitializing();

            $this->firewall = new FirewallProcess($this);
            $this->cfsr     = new CFSRToken();

            if (env('app.firewall.enable', false))
                $this->firewall->execute();
        }

        public function fixMagicQuotesGpc()
        {
            $_SERVER = filter_var_array($_SERVER, FILTER_SANITIZE_STRING);
            $_GET    = filter_var_array($_GET,    FILTER_SANITIZE_STRING);

            if (get_magic_quotes_gpc()) {
                stripcslashesResursive($_GET);
                stripcslashesResursive($_POST);
                stripcslashesResursive($_REQUEST);
                stripcslashesResursive($_COOKIE);
            }
        }

        public function sleepFixHeaderRedirectUrl()
        {
            sleep(env('app.sleep_time_redirect', 2));
        }

        public function sessionInitializing()
        {
            $sessionStart = false;

            if (version_compare(phpversion(), '5.4.0', '>='))
                $sessionStart = session_status() === PHP_SESSION_ACTIVE;
            else
                $sessionStart = session_id() !== '';

            if ($sessionStart == false) {
                session_name         (env('app.session.name',          session_name()));
                session_cache_limiter(env('app.session.cache_limiter', session_cache_limiter()));
                session_cache_expire (env('app.session.cache_expire',  session_cache_expire()));

                session_set_cookie_params(
                    env('app.session.cookie_lifetime', ini_get('session.cookie_lifetime')),
                    env('app.session.cookie_path',     ini_get('session.cookie_path'))
                );

                session_start();
            }
        }

        public function dateInitializing()
        {
            @date_default_timezone_set(env('app.date.timezone', 'Asia/Ho_Chi_Minh'));
        }

        public function obBufferStart()
        {
            if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip'))
                @ob_start('ob_gzhandler');
            else
                @ob_start();

            header('Cache-Control: private, max-age=0, no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s ', time()) . 'GMT');
            header('Etag: "' . md5(time()) . '"');
        }

        public function obBufferClean()
        {
            @ob_clean();
            @ob_end_clean();
        }

        public function obBufferHandler($label, $message, $title = null)
        {
            $path = env('app.path.error') . SP .
                    env('error.handler')  .
                    env('error.mime');

            /*if (FileInfo::isTypeFile($path)) {
                if ($title == null)
                    $title = $label;

                ob_clean();
                ob_flush();

                header('Content-Type: text/html');
                require_once($path);
            }*/
        }

        public function obErrorHandler()
        {
            if (self::isRunLocal() == false)
                return;

            $this->isErrorHandler = false;

            register_shutdown_function(function() {
                $errors = error_get_last();

                if (is_array($errors)) {
                    switch ($errors['type']) {
                        case E_PARSE:
                        case E_ERROR:
                        case E_CORE_ERROR:
                        case E_COMPILE_ERROR:
                        case E_USER_ERROR:
                            $errors['type_string'] = 'Fatal error';
                            break;

                        case E_WARNING:
                        case E_USER_WARNING:
                        case E_COMPILE_WARNING:
                        case E_RECOVERABLE_ERROR:
                            $errors['type_string'] = 'Warning';
                            break;

                        case E_NOTICE:
                        case E_USER_NOTICE:
                            $errors['type_string'] = 'Notice';
                            break;

                        case E_STRICT:
                            $errors['type_string'] = 'Strict';
                            break;

                        case E_DEPRECATED:
                        case E_USER_DEPRECATED:
                            $errors['type_string'] = 'Deprecated';
                            break;

                        default:
                            $errors['type_string'] = 'Unknown';
                            break;
                    }

                    $this->isErrorHandler = true;

                    $this->obBufferHandler(
                        $errors['type_string'] . ' in ' .
                        $errors['file']        . ' on line ' .
                        $errors['line'],
                        $errors['message'],
                        $errors['type_string']
                    );
                }
            });
        }

        public function obBufferEnd()
        {
            if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip'))
                return;

            register_shutdown_function(function() {
                @ob_flush();
                @ob_end_flush();
            });
        }

        public function getEnvironment()
        {
            return $this->environment;
        }

        public function getLanguage()
        {
            return $this->language;
        }

        public function getFirewall()
        {
            return $this->firewall;
        }

        public function getAutoload()
        {
            return $this->autoload;
        }

        public function getCFSRToken()
        {
            return $this->cfsr;
        }

        public static function isRunLocal()
        {
            $host = env('SERVER.HTTP_HOST');

            if (preg_match('/(localhost|127\.0\.0\.1|izerocs\.mobi)(:8080)?/i', $host))
                return true;
        }

    }

?>
