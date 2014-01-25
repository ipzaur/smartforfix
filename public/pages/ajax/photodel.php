<?php
$error = array();

if (empty($_POST['content_id'])) {
    $error[] = 'ERROR_PHOTODEL_NODATA';
    echo json_encode(array('error' => $error));
    die();
}

if (mb_substr($_POST['content_id'], 0, 4) == 'temp') {
    $dirs = explode('/', $_POST['photoSrc']);
    $fileName = array_pop($dirs);
    $filePath = $engine->config['sitepath'] . $engine->config['upload_dir'] . $_POST['content_id'] . '/' . $fileName;
    if (file_exists($filePath)) {
        unlink($filePath);
    }

} else {
    $engine->loadIface('article');
    $getparam = array(
        'id' => $_POST['content_id'],
        'user_id' => $engine->auth->user['id']
    )
    $article = $engine->article->get($getparam);
    if ($article === false) {
        $error[] = 'ERROR_PHOTODEL_NOTOWNER';
        echo json_encode(array('error' => $error));
        die();
    }

    $engine->loadIface('media');
    $whereparam = array(
        'id' => $_POST['photoId'],
        'user_id' => $engine->auth->user['id']
    );
    $fileinfo = $engine->media->get($whereparam);
    if ($fileinfo === false) {
        $error[] = 'ERROR_PHOTODEL_NOTOWNER';
        echo json_encode(array('error' => $error));
        die();
    }
    unlink($engine->config['sitepath'] . $fileinfo['path']);
    $engine->media->delete($whereparam);

    $saveparam = array('content_source' => $article['content_source']);
    $engine->article->save($saveparam, $getparam);
}
echo json_encode(array('error' => $error));
