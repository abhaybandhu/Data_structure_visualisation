<?php
require_once '../DB/dbconnection.php';
require_once "../Classes/users.php";
require_once "../Controller/userHandler.php";
header("Content-Type: application/json");


$handler = new UserHandler();
$result = $handler->logIn("abhaybandhu@gmail.com", "password234");

echo $result;