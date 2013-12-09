<?php
mb_language("ru");
mb_internal_encoding("UTF-8");
require_once '../config.php';
require_once 'iface/iface.core.php';
$engine = new iface_core();
$engine->loadIface('auth');

if ( isset($engine->url[0]) && ($engine->url[0] == '_auth') ) {
    include 'pages/page_auth.php';
}

$engine->tpl->loadTpl('index');
$engine->tpl->addVar('siteurl', $engine->config['siteurl']);
$isMobile = $engine->ismobile->isMobile() ? 'true' : 'false';
$engine->tpl->addVar('isMobile', $isMobile);
$engine->tpl->addVar('noava', $engine->config['noava']);

$current_date = ('Y-m-d H:i:s');
$cur_date = array(
    'Y' => date('Y'),
    'm' => date('m'),
    'd' => date('d'),
    'H' => date('H'),
    'i' => date('i'),
    's' => date('s'),
);

if ( isset($engine->url[0]) && ($engine->url[0] == '_ajax') ) {
    include 'pages/page_ajax.php';
}

if ($engine->auth->user['id'] == 0) {
    $auth_link = array(
        'vk' => array(
            'url'  => $engine->auth->getAuthLink('vk'),
            'text' => 'через Вконтакте'
        )
    );
    $engine->tpl->addvar('auth_link', $auth_link);
}

$engine->loadIface('menu_model');
$engine->tpl->addvar('menu_model', $engine->menu_model->getMenu());
$engine->tpl->addvar('user', $engine->auth->user);
$engine->tpl->addvar('article', 1);

/*
$engine->tpl->addvar('main_page', $main_page);
if ($main_page == 'profile') {
    include 'pages/page_profile.php';
}
*/
$engine->tpl->render();
