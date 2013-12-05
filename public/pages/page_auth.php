<?php
switch ($engine->url[1]) {
    case 'vk'   :
    case 'fb'   :
        $loginparam = array('auth_type' => $engine->url[1]);
        $engine->auth->login($loginparam);
        break;

    case 'out'  :
        $engine->auth->logout();
}
header('Location: ' . $engine->config['siteurl']);
die();
