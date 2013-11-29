<?php
mb_language("ru");
mb_internal_encoding("UTF-8");
require_once '../config.php';
require_once 'iface/iface.core.php';
$engine = new iface_core();
$engine->loadIface('auth');

$current_date = ('Y-m-d H:i:s');
$cur_date = array(
    'Y' => date('Y'),
    'm' => date('m'),
    'd' => date('d'),
    'H' => date('H'),
    'i' => date('i'),
    's' => date('s'),
);
$engine->tpl->addVar('siteurl', $engine->config['siteurl']);
$isMobile = $engine->ismobile->isMobile() ? 'true' : 'false';
$engine->tpl->addVar('isMobile', $isMobile);


if ( isset($engine->url[0]) && ($engine->url[0] == 'ajax') ) {
    include 'pages/page_ajax.php';
}

if ( isset($engine->url[0]) && ($engine->url[0] == 'auth') ) {
    include 'pages/page_auth.php';
}
$engine->tpl->loadTpl('index');

$main_page = ($engine->auth->user['id'] > 0) ? 'profile' : 'intro';
$engine->tpl->addvar('main_page', $main_page);
if ($main_page == 'profile') {
    include 'pages/page_profile.php';
}

$engine->tpl->render();
