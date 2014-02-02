<?php
$error = array();
if ( empty($_POST['content_id']) || empty($_FILES) ) {
    $error[] = 'ERROR_UPLOAD_NODATA';
    echo json_encode(array('error' => $error));
    die();
}

$engine->loadIface('file');
$article_id = $_POST['content_id'];
if (mb_substr($article_id, 0, 4) == 'temp') {
    $upload_type = 'upload';
} else {
    $engine->loadIface('article');
    if ( !$engine->auth->user || !($engine->auth->user['grants'] & 1) ) {
        $error[] = 'ERROR_UPLOAD_PERMISSION';
        echo json_encode(array('error' => $error));
        die();
    }
    $getparam = array(
        'id' => $article_id,
        'user_id' => $engine->auth->user['id']
    );
    $article = $engine->article->get($getparam);
    if ($article == false) {
        $error[] = 'ERROR_UPLOAD_PERMISSION';
        echo json_encode(array('error' => $error));
        die();
    }
    $upload_type = 'article';
    $engine->loadIface('media');
}
$file_path = $engine->config[$upload_type . '_dir'] . $article_id . '/';
$upload_path = $engine->config['sitepath'] . $file_path;

if (file_exists($upload_path)) {
    $files_in_dir = array_diff( scandir($upload_path), array('.', '..') );
    $last_num = intval(array_pop($files_in_dir));
} else {
    mkdir($upload_path, 0777, true);
    $last_num = 0;
}

$files = array();
foreach ($_FILES as $file) {
    if (empty($file['name'])) {
        continue;
    }
    $last_num++;
    $file_name = str_pad($last_num , 3, '0', STR_PAD_LEFT) . '_' . md5(mb_substr($file['name'] . date('Y-m-d H:i:s'), 0, 12));
    $file_name = $engine->file->saveImage($file['tmp_name'], $upload_path . $file_name, 1024, 1024);
    $files[] = $engine->config['siteurl'] . $file_path . $file_name;

    if ($upload_type == 'article') {
        $mediaparam = array(
            'article_id' => $article_id,
            'path' => $file_path . $file_name,
            'user_id' => $engine->auth->user['id']
        );
        $engine->media->save($mediaparam);
    }
}

echo json_encode(array('error' => $error, 'files' => $files));
