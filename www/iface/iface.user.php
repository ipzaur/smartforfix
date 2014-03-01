<?php
/**
 * Interface User
 * Интерфейс для работы с юзерами
 * @author Alexey iP Subbota
 * @version 1.0
 */

class iface_user extends iface_base_entity
{
    public $engine = NULL;

    protected $order_fields = array('name', 'id', 'create_date', 'login_date');
    protected $get_fields = array(
        'id' => array('type' => 'integer', 'many' => 1, 'check_single' => 1, 'notnull' => 1)
    );
    protected $save_fields = array (
        'name' => array('type' => 'string', 'pattern' => '^[А-Яа-яA-Za-z0-9\s]+$', 'notnull' => 1),
        'email' => array('type' => 'string'),
        'avatar' => array('type' => 'string'),
        'about'  => array('type' => 'string'),
        'link_d2'  => array('type' => 'string'),
        'link_own'  => array('type' => 'string'),
        'grants' => array('type' => 'integer'),
        'info450' => array('type' => 'integer'),
        'info451' => array('type' => 'integer'),
        'info452' => array('type' => 'integer'),
        'info454' => array('type' => 'integer'),
        'login_date' => array('type' => 'datetime', 'notnull' => 1),
        'create_date' => array('type' => 'datetime', 'notnull' => 1)
    );
    protected $table_name = 'user';

    public function __construct()
    {
    }

    public function shortInfo($user)
    {
        $result = array(
            'id'       => $user['id'],
            'name'     => $user['name'],
            'avatar'   => $this->engine->config['siteurl'] . $user['avatar'],
            'about'    => $user['about'],
            'link_d2'  => $user['link_d2'],
            'link_own' => $user['link_own'],
            'link_vk'  => ( isset($user['social']['vk']) && $user['social']['vk']['show'] ) ? $user['social']['vk']['url'] : false
        );
        return $result;
    }

    protected function beforeSave(&$saveparam = array(), &$whereparam = array())
    {
        if ( isset($saveparam['about']) && (mb_strlen($saveparam['about']) > 0) ) {
            $saveparam['about'] = preg_replace('~<script>(.*?)</script>~', '', $saveparam['about']);
        }
        return true;
    }

   /**
    * Сохранение аватарки у юзера
    * @param integer id - id юзера
    * @param string source - путь к файлу
    * @result boolean - успех сохранения
    */
    public function saveAvatar($id = 0, $source = '')
    {
        if ($id == 0) {
            return false;
        }
        if ($source == '') {
            return false;
        }

        $getparam = array('id' => $id);
        $user = $this->get($getparam);
        if ($user == false) {
            return false;
        }

        $this->engine->loadIface('file');

        $dest = $this->engine->config['avatar_dir'] . $id . '_' . md5($source . date('H:i:s'));
        $filename = $this->engine->file->saveImage($source, $this->engine->config['sitepath'] . $dest, 200, 200, true);

        if ($user['avatar'] != NULL) {
            unlink($this->engine->config['sitepath'] . $user['avatar']);
        }

        $path = $this->engine->config['avatar_dir'] . $filename;
        $saveparam = array('avatar' => $path);
        $this->save($saveparam, $getparam);

        return $path;
    }

    /**
     * Дополнительная функция по выборке юзеров
     * @param array users - результат выборки. в процессе вернётся изменённый массив с ссылками на соц.профили
     */
    protected function getAfter(&$users = array())
    {
        $single = isset($users['id']);
        if ($single) {
            $users = array($users);
        }

        foreach ($users as &$user) {
            $query = 'SELECT * FROM user_auth WHERE user_id=' . $user['id'];
            $socials = $this->engine->db->query($query);
            if ($socials === false) {
                continue;
            }
            $user['social'] = array();
            foreach ($socials as $social) {
                $user['social'][$social['auth_type']] = array(
                    'id'   => $social['auth_id'],
                    'name' => $social['auth_name'],
                    'url'  => $social['auth_url'],
                    'show' => $social['show']
                );
            }
        }

        if ($single) {
            $users = $users[0];
        }
    }
}
