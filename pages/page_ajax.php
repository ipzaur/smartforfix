<?php
$allow_gates = array('debug', 'menumodels', 'article', 'profile');
if (isset($engine->url[1])) {
    header("Content-type: text/html; charset=utf-8");
    if (in_array($engine->url[1], $allow_gates)) {
        include ('pages/ajax/' . $engine->url[1] . '.php');
    }
}
die();