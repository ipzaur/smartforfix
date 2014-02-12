<?php
$allow_subgates = array('save', 'avatar');
if ( isset($engine->url[2]) && in_array($engine->url[2], $allow_subgates) ) {
    include ('pages/ajax/profile/' . $engine->url[2] . '.php');
}