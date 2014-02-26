<?php
mb_language("ru");
mb_internal_encoding("UTF-8");
require_once '../config.php';
require_once 'iface/iface.core.php';
$engine = new iface_core();

if ( !file_exists('js/s4fx.js') || $engine->config['debug'] ) {
    $before = array('js/jquery203.js', 'js/lightbox.js', 'js/editor.js');

    function grabDir($path)
    {
        global $before;

        $result = '';
        $js_files = scandir($path . '/');
        foreach ($js_files AS $file) {
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

            if (mb_strpos($file, '.js') === false) {
                continue;
            }
            if ( ($path == 'js') && ($file == 's4fx.js') ) {
                continue;
            }
            $result .= file_get_contents($path . '/' . $file) . "\n";
        }
        return $result;
    }

    $result = fopen('js/s4fx.js', 'w');
    fwrite($result, "window.setTimeout(function(){");
    fwrite($result, "var SITEURL = '" . $engine->config['siteurl'] . "';");
    foreach ($before as $file) {
        if (file_exists($file)) {
            fwrite($result, file_get_contents($file));
        }
    }
    fwrite($result,  grabDir('js') . "\n");
    fwrite($result, "},1);");
    fclose($result);
}
echo file_get_contents('js/s4fx.js');
die();
