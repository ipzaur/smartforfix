<?php
if (!$engine->auth->user) {
    die();
}

$profile = array(
    'social' => array('vk' => array('title'=>'Вконтакте'))
);
if (!empty($_POST)) {
    $whereparam = array('id' => $engine->auth->user['id']);
    $engine->user->save($_POST, $whereparam, $error);

    foreach ($profile['social'] as $social_name=>$social) {
        $show = intval( isset($_POST['social']) && isset($_POST['social'][$social_name]) );
        $engine->auth->socialShow($social_name, $show);
    }
    $engine->auth->refresh();
}

foreach ($profile['social'] as $social_name=>&$social) {
    if (!isset($engine->auth->user['social'][$social_name])) {
        $social['social_url'] = $engine->auth->getAuthLink($social_name);
    } else {
        $social = $social + $engine->auth->user['social'][$social_name];
    }
}

$engine->tpl->addvar('profile', $profile);
