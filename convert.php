<?php
mb_language("ru");
mb_internal_encoding("UTF-8");
require_once '../config.php';
require_once './libs/rollbar.php';
require_once 'iface/iface.core.php';
$engine = new iface_core();

$engine->loadIface('article');
$engine->loadIface('article_section');
$articles = $engine->article->get();

foreach ($articles as $article) {
    echo $article['id'] . ' = ' . $article['section']['id'] . "\n";
    $engine->article_section->save([
        'article_id' => $article['id'],
        'section_id' => $article['section']['id'],
    ]);
}