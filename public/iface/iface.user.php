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

        $dest = 'include/avatar/' . md5($id . $source . date('H:i:s')) . '.png';
        $errors = $this->engine->file->saveImage($source, $this->engine->sitepath . $dest, 200, 200, true);

        if ($user['avatar'] != NULL) {
            unlink($this->engine->sitepath . $user['avatar']);
        }

        $saveparam = array('avatar' => $dest);
        $this->save($saveparam, $getparam);
    }
}
