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
if (isset($_POST['search'])) {
    header('Location:' . $engine->siteurl . '/search/' . $_POST['search'] . '/');
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
if (isset($_COOKIE['debug'])) {
    $engine->tpl->addVar('debug', 1);
}
/*
$query = 'curl -XPUT \'http://localhost:9200/smartforfix/articles/_mapping\' -d \'{
            "properties" : {
                "text" : { "type" : "string", "analyzer" : "russian_morphology" }
            }
}\' ';
exec($query, $result);
$engine->vardump($result);
/*$query = 'curl -XPUT \'http://localhost:9200/smartforfix/articles/1\' -d \'
     { 
       "id"   : "1",
       "name" : "HelloWorld",
       "text" : "Лягушки квакушки Hello My World"
     }\' ';
exec($query, $result);
$query = 'curl -XPUT \'http://localhost:9200/smartforfix/articles/3\' -d \'
     { 
       "id"   : "3",
       "name" : "HelloWorld3",
       "text" : "I can show you лягушку"
     }\' ';
exec($query, $result);*/
/*
$query = 'curl -XGET \'http://localhost:9200/smartforfix/articles/_count?q=*&pretty\'';
exec($query, $result);
$engine->vardump($result);
$query = 'curl -XGET \'http://localhost:9200/smartforfix/articles/_search?q=text:(let+show)*&pretty\'';
exec($query, $result);
$result = '[' . implode($result) . ']';
$result = str_replace('}{', '},{', $result);
$engine->vardump( json_decode($result, true) );
*/
if ($engine->auth->user['id'] == 0) {
    $auth_link = array(
        'vk' => array(
            'url'  => $engine->auth->getAuthLink('vk'),
            'text' => 'через Вконтакте'
        )
    );
    $engine->tpl->addVar('auth_link', $auth_link);
}

$engine->loadIface('menu_model');
$menu_models = $engine->menu_model->getMenu();
$engine->tpl->addVar('menu_model', $menu_models);

$engine->loadIface('section');
$sections = $engine->section->get(array('hidden' => 0), array('name'=>'desc'));
$engine->tpl->addVar('section', $sections);

if ( isset($engine->url[0]) && ($engine->url[0] == 'article') ) {
    include 'pages/page_article.php';
} else if ( isset($engine->url[0]) && ($engine->url[0] == 'profile') ) {
    include 'pages/page_profile.php';
} else {
    include 'pages/page_articleList.php';
}
$engine->tpl->addVar('user', $engine->auth->user);

$engine->tpl->render();
