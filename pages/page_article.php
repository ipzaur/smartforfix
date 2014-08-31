<?php
$engine->loadIface('article');
if (isset($engine->url[1])) {
    if ($engine->url[1] == '_edit') {
        include 'article/edit.php';
    } else {
        include 'article/show.php';
    }
}
