<?php
$error = array();
if ( empty($_POST['content_id']) || empty($_FILES) ) {
    $error[] = 'ERROR_UPLOAD_NODATA';
    echo json_encode(array('error' => $error));
    die();
}

$dir_name = $_POST['content_id'];

$engine->loadIface('file');
$file_path = $engine->config['upload_dir'] . $dir_name . '/';
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
    $files[] = $engine->config['siteurl'] . $file_path . $engine->file->saveImage($file['tmp_name'], $upload_path . $file_name);
}

echo json_encode(array('error' => $error, 'files' => $files));
