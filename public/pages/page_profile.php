<?php
if (!$engine->auth->user) {
    die();
}

if (!empty($_POST)) {
    $whereparam = array('id' => $engine->auth->user['id']);
    $engine->user->save($_POST, $whereparam, $error);

    $socials = array('vk', 'fb');
    foreach ($socials as $social) {
        $show = intval( isset($_POST['social']) && isset($_POST['social'][$social]) );
        $engine->auth->socialShow($social, $show);
    }
    $engine->auth->refresh();
}

$profile = array(
    'social' => array('vk' => array('title'=>'Вконтакте'))
);
foreach ($profile['social'] as $social_name=>&$social) {
    $social['text'] = 'Через ' . $social['title'];
    if (!isset($engine->auth->user['social'][$social_name])) {
        $social['url'] = $engine->auth->getAuthLink($social_name);
        $social['link_text'] = 'Связать с ' . $social['title'];
    } else {
        $social = $social + $engine->auth->user['social'][$social_name];
    }
}

$engine->tpl->addvar('profile', $profile);
