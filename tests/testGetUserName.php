<?php
require_once '../DB/dbconnection.php';
require_once "../Classes/users.php";
require_once "../Classes/operation.php";
require "../vendor/autoload.php";
use MongoDB\BSON\ObjectId;

$user = "628b52f4cadf0255529aca32";

echo 'hello';
$result =CommonFunction::checkExistIn(DbTable::Users,["_id"=> new ObjectId($user)], ['lastName'=>1, 'firstName'=>1], true);
echo 'hello';

var_dump($result);