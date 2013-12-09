<?php
if (isset($engine->url[1])) {
    if ($engine->url[1] == '_edit') {
        if ( isset($engine->url[2]) && ($engine->url[2] > 0) ) {
            $article_id = $engine->url[2];
            if (!empty($_POST)) {
            }
        } else {
        }
    }
}