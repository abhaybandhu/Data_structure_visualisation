<?php

if (!isset($_POST['name_test'])){
    echo 'error';
    die();
}

echo "Value entered is : " . $_POST['name_test'];