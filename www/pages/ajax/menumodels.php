<?php
$error = array();

$engine->loadIface('menu_model');
$result = $engine->menu_model->saveMenu($_POST);

echo json_encode(array('error' => $error, 'result' => $result));
