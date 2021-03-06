<?php
$error = array();

$engine->loadIface('article');

if (!$engine->auth->user) {
    $error[] = 'ERROR_ARTICLE_NOTREGISTERED';
    echo json_encode(array('error' => $error, 'result' => $result));
    die();
}

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
if (isset($saveparam['tag'])) {
    $saveparam['tag'] = explode(',', $saveparam['tag']);
}
$article_id = $engine->article->save($saveparam, $getparam);

$json = array(
    'error' => $error,
    'result' => $article_id
);
if ($saveparam['id'] == 0) {
    $engine->loadIface('media');
    $getparam = array('article_id' => $article_id);
    $json['photos'] = $engine->media->get($getparam);
}
echo json_encode($json);

