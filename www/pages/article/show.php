<?php
$getparam = array('url' => $engine->url[1]);
$article = $engine->article->get($getparam);
if ($article === false) {
    die();
}
$engine->tpl->addVar('article', $article);
$userList = array( $article['user']['id'] => $engine->user->shortInfo($article['user']) );
$engine->tpl->addVar('JS_userList', json_encode($userList));
$engine->tpl->addVar('title', $article['name']);
