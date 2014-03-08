<?php
if ( !$engine->auth->user || !($engine->auth->user['grants'] & 2) ) {
    die();
}

if ( isset($engine->url[2]) && ($engine->url[2] > 0) ) {
    $getparam = array(
        'id' => $engine->url[2],
        'user_id' => $engine->auth->user['id']
    );
    $article = $engine->article->get($getparam);
    if ($article === false) {
        die();
    }
    $content_id = $article['id'];

    $engine->loadIface('media');
    $getparam = array(
        'article_id' => $getparam['id'],
        'user_id'    => $getparam['user_id'],
        'hidden'     => 0
    );
    if ($article['media'] !== false) {
        $photos = array();
        $photo_num = 1;
        foreach ($article['media'] as $file) {
            $photos[$photo_num] = array(
                'id' => $file['id'],
                'path' => $file['path']
            );
            $photo_num++;
        }
        $engine->tpl->addVar('photos', $photos);
    }
} else {
    $article = array(
        'id'   => 0,
        'type' => (-1)
    );
    if (isset($_COOKIE['temp_article'])) {
        $content_id = $_COOKIE['temp_article'];

        $file_path = $engine->config['upload_dir'] . $content_id . '/';
        $upload_path = $engine->config['sitepath'] . $file_path;

        if (file_exists($upload_path)) {
            $files = array_diff( scandir($upload_path), array('.', '..') );
            if (count($files) > 0) {
                $photos = array();
                $photo_num = 1;
                foreach ($files as $file) {
                    $photos[$photo_num] = array('path' => $file_path . $file);
                    $photo_num++;
                }
                $engine->tpl->addVar('photos', $photos);
            }
        }
    } else {
        $content_id = 'temp_' . mb_substr(md5($engine->auth->user['id'] . ' ' . date('Y-m-d H:i:s')), 0, 12);
        setcookie('temp_article', $content_id, time() + 30240000, '/', $engine->config['sitedomain']);
    }
}
$engine->tpl->addVar('article_edit', $article);
$engine->tpl->addVar('content_id', $content_id);
