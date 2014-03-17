<?php
if (!isset($_COOKIE['debug'])) {
    setcookie('debug', '1', time() + 30240000, '/', $engine->config['sitedomain']);
    $status = true;
} else {
    setcookie('debug', null, -1, '/', $engine->config['sitedomain']);
    $status = false;
}
echo json_encode(array('status' => $status));
