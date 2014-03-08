<?php
$params = explode('/', $_GET['q']);
$allowSizes = array(
    '0x70'
);
if (!in_array($params[1], $allowSizes)) {
    echo 'wrong size';
    die();
}

$size = explode('x', $params[1]);
$width = (isset($size[0])) ? $size[0] : false;
$height = (isset($size[1])) ? $size[1] : false;
$crop = (isset($size[2])) ? $size[2] : false;
$dir = $engine->config['sitepath'] . '_r/' . $params[1] . '/';
unset($params[0]);
unset($params[1]);
$origFile = $engine->config['sitepath'] . implode('/', $params);
$fileName = array_pop($params);
$dir .= implode('/', $params) . '/';
if (!file_exists($dir)) {
    mkdir($dir, 0777, true);
}

$engine->loadIface('file');
$engine->file->saveImage($origFile, $dir . $fileName, $width, $height, $crop);

$imginfo = getimagesize($dir . $fileName);
header('Content-type: ' . $imginfo['mime']);
echo file_get_contents($dir . $fileName);
