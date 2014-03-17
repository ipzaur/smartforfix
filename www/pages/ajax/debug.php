<?php
if (!isset($_COOKIE['debug'])) {
    setcookie('debug', '1', time() + 30240000, '/', $engine->config['sitedomain']);
} else {
    setcookie('debug', null, -1, '/', $engine->config['sitedomain']);
}
echo 1;
