<?php
$error = array();

$engine->loadIface('article');

$saveparam = $_POST;
if ($saveparam['id'] > 0) {
    $getparam = array('id' => $saveparam['id']);
    $article = $engine->article->get($getparam);
    if ( ($article === false) || ($article['user_id'] != $engine->auth->user['id']) ) {
        $error[] = 'ERROR_ARTICLE_NOTOWNER';
        echo json_encode(array('error' => $error, 'result' => $result));
        die();
    }
}

$models = array(450, 451, 452, 454);
foreach ($models as $model) {
    $saveparam['info' . $model] = ( !isset($saveparam['info']) || in_array($model, $saveparam['info']) ) ? 1 : 0;
}
if ($saveparam['id'] == 0) {
    $saveparam['user_id'] = $engine->auth->user['id'];
    $getparam = false;
}
$article_id = $engine->article->save($saveparam, $getparam);

echo json_encode(array('error' => $error, 'result' => $article_id));

