<?php
/**
 * Interface Auth
 * Интерфейс, отвечающий за аутентификацию
 * Зависимости: curl, user
 * @author Alexey iP Subbota
 * @version 1.0
 */
define('ERROR_AUTH_ALREADY', 2);
define('ERROR_WRONG_LOGINNAME', 4);
define('ERROR_WRONG_SHORTPASSWORD', 8);
define('ERROR_USER_NOTFOUND', 16);


class iface_auth
{
    public $user = NULL;
    public $engine = NULL;
    private $session = array();

    public function logout()
    {
        if (!isset($this->user['id'])) {
            return false;
        }
        $this->killSession($this->session);
        return true;
    }


    public function login($param = false)
    {
        $error = 0;

        if ( isset($this->user['user_type']) && ($this->user['user_type'] > 1) ) {
            $error |= ERROR_AUTH_ALREADY;
        }

        if ($param['auth_type'] == 'self') {
            if ( !isset($param['login']) || preg_match('~[<>\$\&\%\#\?\*\;\:\(\)]~su', $param['login']) ) {
                $error = $error | ERROR_WRONG_LOGINNAME; // логин содержит не только лат.буквы и цифры
            }

            if ( !isset($param['password']) || (strlen($param['password']) < 5) ) {
                $error = $error | ERROR_WRONG_SHORTPASSWORD; // пароль менее 5 символов
            }

            if ($error > 0) {
                return $error;
            }

            $query = 'SELECT id FROM selfauth WHERE login="' . mysql_escape_string($param['login']) . '" AND password="' . mysql_escape_string(md5($param['password'])) . '"';
            $selfauth_id = $this->engine->db->query($query, 'single');

            if ($selfauth_id == false) {
                $username = mb_substr($param['login'], 0, mb_strpos($param['login'], '@'));
                $username = preg_replace('~[^А-Яа-яA-Za-z0-9]~su', '', $username);
                $userparam = array(
                    'name'        => $username,
                    'email'       => $param['login'],
                    'login_date'  => date('Y-m-d'),
                    'admin'       => 0
                );

                $user_id = $this->engine->user->save($userparam);

                $query = 'INSERT INTO selfauth SET login="' . mysql_escape_string($param['login']) . '", password="' . mysql_escape_string(md5($param['password'])) . '"';
                $selfauth_id = $this->engine->db->query($query);
                $query = 'INSERT INTO user_auth SET auth_type="self", auth_id=' . intval($selfauth_id) . ', user_id='. intval($user_id);
                $this->engine->db->query($query);
            } else {
                $query = 'SELECT user_id FROM user_auth WHERE auth_type="self" AND auth_id=' . intval($selfauth_id);
                $user_id = $this->engine->db->query($query, 'single');
            }
        } else {
            $user_id = 0;
        }

        $getparam = array('id' => $user_id);
        $user = $this->engine->user->get($getparam);

        if ($user == false) {
            $error |= ERROR_USER_NOTFOUND;
        }

        if ($error > 0) {
            return $error;
        }

        $this->user = $user;

        $saveparam = $this->session;
        $saveparam['user_id'] = $this->user['id'];
        $this->saveSession($saveparam);

        return $error;
    }


    private function killSession($param)
    {
        if (isset($param['id'])) {
            $query = 'DELETE FROM session WHERE id = "' . mysql_escape_string($param['id']) . '"';
            $this->engine->db->query($query);
            setcookie('session_id', '', time() + 30240000, '/', $this->engine->sitedomain);
        } else if (isset($param['user_id'])) {
            $query = 'DELETE FROM session WHERE user_id = ' . intval($param['user_id']);
            $this->engine->db->query($query);
            setcookie('session_id', '', time() + 30240000, '/', $this->engine->sitedomain);
        }
    }


    private function &saveSession($param)
    {
        if (isset($param['id']) && isset($param['user_id']) && ($param['user_id'] > 0) ) {
            $query = 'UPDATE session SET
                        user_id=' . intval($param['user_id']) . '
                      WHERE
                        id = "' . mysql_escape_string($param['id']) . '"';
            $this->engine->db->query($query);
        } else {
            $param['id'] = mb_substr(md5(time() * rand(1, 100)), 0 , 220);
            $query = 'INSERT INTO session SET ' .
                        'id = "' . mysql_escape_string($param['id']) . '", ' .
                        'browser = "' . mysql_escape_string($param['browser']) . '", '.
                        'ipaddress = "' . mysql_escape_string($param['ipaddress']) . '"';
            $this->engine->db->query($query);
            $param['user_id'] = 0;
            setcookie('session_id', $param['id'], time() + 30240000, '/', $this->engine->sitedomain);
        }

        return $param;
    }


    private function &getSession()
    {
        $param = array(
            'browser'   => mb_substr($_SERVER['HTTP_USER_AGENT'], 0, 190),
            'ipaddress' => $_SERVER['REMOTE_ADDR']
        );

        if (!isset($_COOKIE['session_id'])) {
            return $this->saveSession($param);
        }

        $param['id'] = $_COOKIE['session_id'];

        $query = 'SELECT * FROM session WHERE id = "' . mysql_escape_string($param['id']) . '"';
        $session = $this->engine->db->query($query, 'row');

        if ( ($session['browser'] != $param['browser']) || ($session['ipaddress'] != $param['ipaddress']) ) {
            $this->killSession($param);
            $session = $this->saveSession($param);
        }
        return $session;
    }


    public function __construct()
    {
    }


    /**
     * Повторно забрать инфу о юзере из БД.
     */
    public function refresh()
    {
        if ( !isset($this->user['id']) || ($this->user['id'] == 0) ) {
            return false;
        }

        $getparam = array('id' => $this->user['id']);
        $this->user = $this->engine->user->get($getparam);
    }


    public function start()
    {
        $this->engine->loadIface('user');
        $this->engine->loadIface('curl');

        $this->session = $this->getSession();
        if ( isset($this->session['user_id']) && ($this->session['user_id'] > 0) ) {
            $getparam = array('id' => $this->session['user_id']);
            $this->user = $this->engine->user->get($getparam);
            $saveparam = array('login_date' => date('Y-m-d H:i:s'));
            $this->engine->user->save($saveparam, $getparam);
        }
    }
}
