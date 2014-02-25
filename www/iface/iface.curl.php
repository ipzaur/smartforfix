<?php
/**
 * Interface CURL
 * Интерфейс для выполнения всяких запросов (GET, POST)
 * @author Alexey iP Subbota
 * @version 1.0
 */
class iface_curl
{
    public $engine = NULL;

    public function send($url = false, $post = false, $param = false)
    {
        $error = 0;
        if ($url === false) {
            $error |= ERROR_NO_PARAM;
        }

        if ($error > 0) {
            return $error;
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_USERAGENT, 'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.9.2.9) Gecko/20100824 Firefox/3.6.9');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        if ($post !== false) {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        }
        if (isset($param['headers'])) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $options['headers']);
        }
        $result = trim(curl_exec($curl));
        if ( isset($param['with_info']) && ($param['with_info'] > 0) ) {
            $result = array(
                'info'    => curl_getinfo($curl),
                'content' => $result
            );
        }
        curl_close($curl);

        return $result;
    }

    public function __construct()
    {
    }
}
