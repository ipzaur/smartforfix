<?php
mb_language("ru");
mb_internal_encoding("UTF-8");
require_once '../config.php';
require_once 'iface/iface.core.php';
$engine = new iface_core();

if ( isset($engine->url[0]) && ($engine->url[0] == '_r') ) {
    include 'resizer.php';
    die();
}


$engine->loadIface('auth');

if ( isset($engine->url[0]) && ($engine->url[0] == '_auth') ) {
    include 'pages/page_auth.php';
}


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

$engine->tpl->loadTpl('index');
$engine->tpl->addVar('siteurl', $engine->config['siteurl']);
$isMobile = $engine->ismobile->isMobile() ? 'true' : 'false';
$engine->tpl->addVar('isMobile', $isMobile);
$engine->tpl->addVar('noava', $engine->config['noava']);

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
$menu_models = $engine->menu_model->getMenu();
$engine->tpl->addvar('menu_model', $menu_models);

$engine->loadIface('section');
$sections = $engine->section->get(array('hidden' => 0));
$engine->tpl->addvar('section', $sections);

$engine->tpl->addvar('user', $engine->auth->user);

if ( isset($engine->url[0]) && ($engine->url[0] == 'article') ) {
    include 'pages/page_article.php';
} else {
    include 'pages/page_articleList.php';
}

$engine->tpl->render();
