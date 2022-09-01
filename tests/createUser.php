<?php

require_once '../DB/dbconnection.php';
require_once "../Classes/users.php";
require_once '../Controller/userHandler.php';
$handler = new UserHandler();
$data = array(
    "first"    => 'Abhay',
    "last"     => 'Bandhu',
    "email"    => 'abhaybandhu7@gmail.com',
    "type"     => 'student',
    "pass"     => 'password1234'
);
$inserted =$handler->signIn($data);

echo $inserted;
// if ($inserted['success']){
//     echo "\n inserted";
// }else{
//     echo "\n Error in insertion or email already exists: ". $inserted['error'];

// }