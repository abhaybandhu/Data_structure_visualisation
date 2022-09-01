<?php
require "../vendor/autoload.php";
require_once '../DB/dbconnection.php';
require_once "../Classes/users.php";
require_once '../Controller/userHandler.php';
use MongoDB\BSON\ObjectId;
$handler = new UserHandler();
$timestamp =(new Datetime("now"))->getTimeStamp();

//echo $handler->checkexist(DbTable::Classroom, ["_id"=> new ObjectId("629109c4f042076182ad117d")], ['_id' =>1, 'code'=>1]);
echo $timestamp.'<br/>';
echo new MongoDB\BSON\UTCDateTime($timestamp*1000).'<br/>';
echo 1653630060000;