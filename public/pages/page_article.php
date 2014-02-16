<?php
$engine->loadIface('article');
if (isset($engine->url[1])) {
    if ($engine->url[1] == '_edit') {
        include 'article/edit.php';
    } else {
        $getparam = array('url' => $engine->url[1]);
        $article = $engine->article->get($getparam);
        if ($article !== false) {
            $engine->tpl->addVar('article', $article);
            $userList = array( $article['user']['id'] => $engine->user->shortInfo($article['user']) );
            $engine->tpl->addvar('JS_userList', json_encode($userList));
        }
    }
}