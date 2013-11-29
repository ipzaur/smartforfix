<?php
$debug = true;

mb_language("ru");
mb_internal_encoding("UTF-8");
header("Content-type: text/css; charset=utf-8");
require_once '../config.php';
require_once 'iface/iface.core.php';
$engine = new iface_core();

if ( ($debug == true) || !file_exists('css/s4fx.css') ) {
    $before = array('common.css');

    function grabDir($path)
    {
        global $before;

        $result = '';
        $css_files = scandir($path . '/');
        foreach ($css_files AS $file) {
            if ( ($file == '.') || ($file == '..') ) {
                continue;
            }
            if (is_dir($path . '/' . $file)) {
                $result .= grabDir($path . '/' . $file);
                continue;
            }
            if (in_array($path . '/' . $file, $before)) {
                continue;
            }

            if (mb_strpos($file, '.css') === false) {
                continue;
            }
            if ( ($path == 'css') && ($file == 's4fx.css') ) {
                continue;
            }
            $result .= file_get_contents($path . '/' . $file) . "\n";
        }
        return $result;
    }

    $result = fopen('css/s4fx.css', 'w');
    foreach ($before as $file) {
        if (file_exists($file)) {
            fwrite($result, file_get_contents($file));
        }
    }
    fwrite($result,  grabDir('css') . "\n");
    fclose($result);
}
echo file_get_contents('css/s4fx.css');
die();