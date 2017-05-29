<?php

    namespace Librarys\App;

    use Librarys\Boot;
    use Librarys\File\FileInfo;
    use Librarys\CFSR\CFSRToken;

    final class AppUser
    {

        private $boot;
        private $db;

        private $id;
        private $user;
        private $token;

        private $isLogin;

        const KEY_USERNAME = 'username';
        const KEY_PASSWORD = 'password';
        const KEY_POSITION = 'position';

        const KEY_TIME_CREATE = 'time_create';
        const KEY_TIME_MODIFY = 'time_modify';
        const KEY_TIME_LOGIN  = 'time_login';

        const POSITION_BAN         = 0;
        const POSITION_USER        = 1;
        const POSTION_ADMIN        = 2;
        const POSTION_ADMINSTRATOR = 4;

        public function __construct(Boot $boot, $db)
        {
            $this->boot = $boot;
            $this->parse($db);
        }

        public function execute()
        {
            if (isset($_SESSION[env('app.login.session_login_name')]) && isset($_SESSION[env('app.login.session_token_name')])) {
                $id    = intval($_SESSION[env('app.login.session_login_name')]);
                $token = addslashes($_SESSION[env('app.login.session_token_name')]);

                if (isset($this->db[$id])) {
                    $this->user    = $this->db[$id];
                    $this->isLogin = true;
                } else {
                    $this->isLogin = false;
                }
            } else {
                $this->isLogin = false;
            }
        }

        public function get($key)
        {
            if ($this->isLogin() && is_array($this->user)) {
                if (array_key_exists($key, $this->user))
                    return $this->user[$key];
            }

            return null;
        }

        public function getID()
        {
            return $this->id;
        }

        public function getToken()
        {
            return $this->token;
        }

        public function isLogin()
        {
            return $this->isLogin;
        }

        public function parse($db)
        {
            if (is_null($db))
                return;

            if (is_array($db))
                $this->db = $db;
            else if (FileInfo::isTypeFile($db))
                $this->db = require_once($db);
            else if (is_null($db) == false)
                $this->db = json_decode($db, true);
            else
                $this->db = array();
        }

        public function isUser($username, $password, $passwordEncode = true)
        {
            if (is_array($this->db) && count($this->db) > 0) {
                $username = strtolower($username);

                if ($passwordEncode)
                    $password = self::passwordEncode($password);

                foreach ($this->db AS $id => $user) {
                    if ($username == strtolower($user[self::KEY_USERNAME]) && $password == $user[self::KEY_PASSWORD])
                        return $user;
                }
            }

            return false;
        }

        public function createSessionUser($id, $token = null)
        {
            if ($token == null)
                $token = CFSRToken::generator();

            $_SESSION[env('app.login.session_login_name')] = intval($id);
            $_SESSION[env('app.login.session_token_name')] = addslashes($token);

            $this->execute();
        }

        public static function passwordEncode($password)
        {
            return md5(md5(base64_encode($password)));
        }

    }

?>