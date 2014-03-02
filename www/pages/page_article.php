<?php
$engine->loadIface('article');
if (isset($engine->url[1])) {
    if ($engine->url[1] == '_edit') {
        include 'article/edit.php';
    } else {
        $getparam = array('url' => $engine->url[1]);
        $article = $engine->article->get($getparam);
        if ($article === false) {
            die();
        }
        $engine->tpl->addVar('article', $article);
        $userList = array( $article['user']['id'] => $engine->user->shortInfo($article['user']) );
        $engine->tpl->addVar('JS_userList', json_encode($userList));
        $engine->tpl->addVar('title', $article['name']);
    }
}