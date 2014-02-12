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

$engine->tpl->addvar('profile', true);
