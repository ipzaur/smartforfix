<?php
$error = array();

$engine->loadIface('fav');

if (!$engine->auth->user) {
    $error[] = 'ERROR_ARTICLE_NOTREGISTERED';
    echo json_encode(array('error' => $error, 'result' => $result));
    die();
}

if ( !isset($_POST['article_id']) || ($_POST['article_id'] == 0) ) {
    $error[] = 'ERROR_ARTICLE_NOID';
    echo json_encode(array('error' => $error, 'result' => $result));
    die();
}

$params = array(
    'article_id' => $_POST['article_id'],
    'user_id' => $engine->auth->user['id']
);
$fav = $engine->fav->get($params);
if ($fav !== false) {
    $engine->fav->delete($params);
} else {
    $engine->fav->save($params);
}

echo json_encode(array('error' => $error));
