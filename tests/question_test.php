<?php
//629109c4f042076182ad117d
require_once '../DB/dbconnection.php';
require_once "../Classes/question.php";
require_once "../Classes/operation.php";

$ques = new PostedQuestion('629109c4f042076182ad117d');
$array = $ques->fetch();
foreach($array->data as $item){
    var_dump($item);
}