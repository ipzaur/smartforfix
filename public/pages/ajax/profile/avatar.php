<?php
$error = array();
if ($engine->auth->user['id'] == 0) {
    $error[] = 'ERROR_UPLOAD_PERMISSION';
    echo json_encode(array('error' => $error));
    die();
}

$path = $engine->user->saveAvatar($engine->auth->user['id'], $_FILES[0]['tmp_name']);
$path = 'http://' . $engine->config['sitedomain']  . '/' . $path;

echo json_encode(array('error' => $error, 'result' => $path));
