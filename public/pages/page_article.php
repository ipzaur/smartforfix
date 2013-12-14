<?php
$engine->loadIface('article');
if (isset($engine->url[1])) {
    if ($engine->url[1] == '_edit') {
        if ( isset($engine->url[2]) && ($engine->url[2] > 0) ) {
            $getparam = array('id' => $engine->url[2]);
            $article = $engine->article->get($getparam);
        } else {
            $article = array(
                'id'   => 0,
                'type' => 0
            );
        }
        $engine->tpl->addVar('articleEdit', $article);
    } else {
        $getparam = array('url' => $engine->url[1]);
        $article = $engine->article->get($getparam);
        if ($article !== false) {
            $engine->tpl->addVar('article', $article);
        }
    }
}