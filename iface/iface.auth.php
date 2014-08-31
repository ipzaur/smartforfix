<?php
/**
 * Interface Auth
 * Интерфейс, отвечающий за аутентификацию
 * Зависимости: curl, user
 * @author Alexey iP Subbota
 * @version 1.0
 */
define('ERROR_USER_NOTFOUND', 2);
define('ERROR_AUTH_ALREADY', 4);


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

    public function getAuthLink($social = null)
    {
        if ($social == 'vk') {
            $query = array(
                'client_id'     => $this->engine->config['auth']['vk']['app_id'],
                'redirect_uri'  => $this->engine->config['auth']['vk']['redirect'],
                'response_type' => 'code'
            );

            return 'http://oauth.vk.com/authorize?' . urldecode(http_build_query($query));
        }

        return $this->engine->sitedomain;
    }


    public function login($param = false)
    {
        $error = 0;

        if ( isset($this->user['user_type']) && ($this->user['user_type'] > 1) ) {
            $error |= ERROR_AUTH_ALREADY;
        }

        if ($param['auth_type'] == 'vk') {
            if (!isset($_GET['code'])) {
                return false;
            }
            $query = array(
                'client_id'     => $this->engine->config['auth']['vk']['app_id'],
                'client_secret' => $this->engine->config['auth']['vk']['app_key'],
                'code'          => $_GET['code'],
                'redirect_uri'  => $this->engine->config['auth']['vk']['redirect']
            );
            $token = json_decode(file_get_contents('https://oauth.vk.com/access_token?' . urldecode(http_build_query($query))), true);

            $query = 'SELECT user_id FROM user_auth WHERE auth_type="vk" AND auth_id="' . mysql_escape_string($token['user_id']) . '"';
            $user_id = $this->engine->db->query($query, 'single');
            if ($user_id === false) {
                if (!isset($token['access_token'])) {
                    return false;
                }
                $query = array(
                    'uids'         => $token['user_id'],
                    'fields'       => 'uid,first_name,last_name,photo_max,domain',
                    'access_token' => $token['access_token']
                );
                $info = json_decode(file_get_contents('https://api.vk.com/method/users.get?' . urldecode(http_build_query($query))), true);
                $info = $info['response'][0];
                $auth_name = $info['first_name'] . ' ' . $info['last_name'];

                $saveparam = array(
                    'name'        => $auth_name,
                    'login_date'  => date('Y-m-d H:i:s'),
                    'grants'      => 1
                );
                $user_id = $this->engine->user->save($saveparam);

                $avatar = file_get_contents($info['photo_max']);
                $filetype = array_pop(explode('.', $info['photo_max']));
                $filepath = $this->engine->config['avatar_dir'] . $user_id . '_' . mb_substr(md5(date('H:i:s')), 0, 6) . '.' . $filetype;
                $saveparam = array('avatar' => $filepath);
                $whereparam = array('id' => $user_id);
                $this->engine->user->save($saveparam, $whereparam);
                
                file_put_contents($this->engine->config['sitepath'] . $filepath, $avatar);

                $query = 'INSERT INTO user_auth SET auth_type="vk", auth_id="' . $token['user_id'] . '", auth_name="' . mysql_escape_string($auth_name) . '", auth_url="https://vk.com/' . $info['domain'] . '", user_id='. intval($user_id);
                $this->engine->db->query($query);
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
            setcookie('session_id', '', time() + 30240000, '/', $this->engine->config['sitedomain']);
        } else if (isset($param['user_id'])) {
            $query = 'DELETE FROM session WHERE user_id = ' . intval($param['user_id']);
            $this->engine->db->query($query);
            setcookie('session_id', '', time() + 30240000, '/', $this->engine->config['sitedomain']);
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
            setcookie('session_id', $param['id'], time() + 30240000, '/', $this->engine->config['sitedomain']);
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
        if (isset($this->engine->config['auth'])) {
            foreach ($this->engine->config['auth'] as $social=>&$config) {
                if (mb_strpos($config['redirect'], 'http') === false) {
                    if (mb_strpos($config['redirect'], $this->engine->config['sitedomain']) === false) {
                        $config['redirect'] = $this->engine->config['sitedomain'] . $config['redirect'];
                    }
                    $config['redirect'] = 'http://' . $config['redirect'];
                }
            }
        }

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

    public function socialShow($social_type = null, $show = true)
    {
        if (!$this->user) {
            return false;
        }
        $allow_types = array('vk', 'fb');
        if (!$social_type || !in_array($social_type, $allow_types)) {
            return false;
        }
        $query = 'SELECT * FROM user_auth WHERE user_id=' . $this->user['id'] . ' AND auth_type="' . $social_type . '"';
        $has_auth = $this->engine->db->query($query);
        if (!$has_auth) {
            return false;
        }
        $query = 'UPDATE user_auth ua SET ua.show=' . intval($show) . ' WHERE ua.user_id=' . $this->user['id'] . ' AND ua.auth_type="' . $social_type . '"';
        $this->engine->db->query($query);

        return true;
    }
}
