<?php
$allow_subgates = array('save', 'fav', 'photodel', 'upload');
if ( isset($engine->url[2]) && in_array($engine->url[2], $allow_subgates) ) {
    include ('pages/ajax/article/' . $engine->url[2] . '.php');
}