<?php

    namespace Librarys\App;

    use Librarys\Boot;
    use Librarys\File\FileInfo;

    final class AppAlert
    {

        private $boot;
        private $id;
        private $langMsg;

        const SESSION_NAME_PREFIX = 'ALERT_';

        const DANGER  = 'danger';
        const SUCCESS = 'success';
        const WARNING = 'warning';
        const INFO    = 'info';
        const NONE    = 'none';

        public function __construct(Boot $boot, $alertDefine)
        {
            $this->boot = $boot;

            if (FileInfo::isTypeFile($alertDefine))
                require_once($alertDefine);
        }

        public function danger($message, $id = null, $urlGoto = null)
        {
            $this->add($message, self::DANGER, $id, $urlGoto);
        }

        public function success($message, $id = null, $urlGoto = null)
        {
            $this->add($message, self::SUCCESS, $id, $urlGoto);
        }

        public function warning($message, $id = null, $urlGoto = null)
        {
            $this->add($message, self::WARNING, $id, $urlGoto);
        }

        public function info($message, $id = null, $urlGoto = null)
        {
            $this->add($message, self::INFO, $id, $urlGoto);
        }

        public function add($message, $type = self::DANGER, $id = null, $urlGoto = null)
        {
        	$this->boot->sessionInitializing();

            if ($id == null) {
                if ($this->id == null)
                    $this->id = time();

                $id = $this->id;
            }

            if ($message == null && $this->langMsg != null)
                $message = $this->langMsg;

            $_SESSION[self::SESSION_NAME_PREFIX . $id][] = [
                'message' => $message,
                'type'    => $type
            ];

            if ($urlGoto !== null)
                gotoURL($urlGoto);
        }

        public function display()
        {
            if ($this->id != null && isset($_SESSION[self::SESSION_NAME_PREFIX . $this->id]) && count($_SESSION[self::SESSION_NAME_PREFIX . $this->id]) > 0) {
                $array  = $_SESSION[self::SESSION_NAME_PREFIX . $this->id];
                $buffer = '<ul class="alert">';

                foreach ($array AS $index => $alert) {
                    if (is_object($alert['message']))
                        $alert['message'] = 'Object';
                    else if (is_array($alert['message']))
                        $alert['message'] = 'Array';

                    $buffer .= '<li class="' . $alert['type'] . '">';
                    $buffer .= '<span>' . $alert['message'] . '</span>';
                    $buffer .= '</li>';
                }

                $buffer .= '</ul>';

                echo($buffer);
                unset($_SESSION[self::SESSION_NAME_PREFIX . $this->id]);
            }
        }

        public function setID($id)
        {
            $this->id = $id;
        }

        public function setLangMsg($key)
        {
            $args = func_get_args();
            $nums = func_num_args();

            if ($nums <= 1)
                $args = [];
            else
                $args = array_splice($args, 1, $nums);

            $this->langMsg = lng($key, $args);
        }

        public function removeLangMsg()
        {
            $this->langMsg = null;
        }

        public function getLangMsg()
        {
            return $this->langMsg;
        }

        public function gotoURL($url)
        {
            gotoURL($url);
        }

    }

?>