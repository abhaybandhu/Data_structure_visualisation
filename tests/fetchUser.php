<?php
//test 
require_once '../DB/dbconnection.php';
require_once "../Classes/users.php";

header("Content-Type: application/json");

$conn = new DataBase();
$retrievData= $conn->FetchAll(DbTable::Users);
// echo json_encode($retrievData, JSON_PRETTY_PRINT);
$users = array();
foreach ($retrievData as $data){
    array_push($users, new User($data));
}


echo $users[0]->getJson();
// echo $joe->getJson();

// echo json_encode($users[0]->getObject(), JSON_PRETTY_PRINT);
