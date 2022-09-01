<?php
//error_reporting, error_log , display_error

var_dump(ini_get('error_reporting'));
var_dump(E_ALL);
var_dump(E_WARNING);
var_dump(ini_get('file_upload'));

// ini_set('error_reporting', E_ALL &~ E_WARNING);
$array = [1];

echo $array[3];