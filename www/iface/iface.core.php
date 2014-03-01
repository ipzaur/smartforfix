<?php
/**
 * Interface Core
 * Интерфейс, содержащий рутинный функционал
 * @author Alexey iP Subbota
 * @version 0.1
 */
define('ERROR_NO_PARAM', '1');

class iface_core
{
    public $config;
    public $url;
    public $last_error = false;

    /**
     * Конструктор
     */
    public function __construct()
    {
        global $config;

        $this->url = $this->parseUrl($_SERVER['REQUEST_URI']);
        $this->config = $config;
        $this->config['siteurl'] = 'http://' . $this->config['sitedomain'] . '/';

        $this->loadIface('db');
        $this->loadIface('tpl');
        $this->loadIface('base_entity');
        $this->loadIface('ismobile');
    }


    /**
     * Подключение сторонних интерфейсов
     * @param string iface_name - имя интерфейса
     * @result boolean
     */
    public function loadIface($iface_name = false)
    {
        if ($iface_name === false) {
            return false;
        }

        if (isset($this->$iface_name)) {
            return true;
        }

        $iface_path = $this->config['sitepath'] . 'iface/iface.' . $iface_name . '.php';
        if (!file_exists($iface_path)) {
            return false;
        }

        require_once($iface_path);
        $iface_class = 'iface_' . $iface_name;

        $this->$iface_name = new $iface_class();
        $this->$iface_name->engine = &$this;
        if (method_exists($this->$iface_name, 'start')) {
            $this->$iface_name->start();
        }

        return true;
    }


    /**
     * var_dump, обёрнутый в тэг pre
     * @param mixed variable - переменная для var_dump'а
     */
    public function vardump($variable, $varname = '')
    {
        echo '<xmp style="background:white;padding:10px 0;font-size:12px;color:black;">';
        if ($varname != '') {
            echo '=[ ' . $varname . " ]=\n";
        }
        var_dump($variable);
        echo '</xmp>';
    }


    /**
     * Разбиение урл-запроса на части
     * @param string unparsed_uri - урл-запрос
     * @result mixed
     */
    private function &parseUrl($unparsed_url)
    {
        $result = array();
        if ($unparsed_url == '/') {
            return $result;
        }
        $unparsed_url = trim($unparsed_url, '/');
        $result = explode('/', $unparsed_url);
        $last = array_pop($result);
        if (mb_strpos($last, '?') !== false) {
            list($last, $get) = explode('?', $last);
        }
        array_push($result, $last);
        return $result;
    }
}