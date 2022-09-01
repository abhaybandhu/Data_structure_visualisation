<?php

require_once '../DB/dbconnection.php';
require_once "../Classes/users.php";
require_once "../Classes/operation.php";
require "../vendor/autoload.php";
// header("Content-Type: application/json");

use MongoDB\BSON\ObjectId;
$class_id = "629109c4f042076182ad117d";
$returnObj = null;
$searchQuery = [
    'class_id'  => new ObjectId($class_id),
];
$project=[
    'class_id'  => 0
];
$data = null;
try{
    $db = new DataBase();
    $data =$db->projection(DbTable::ClassPost, $searchQuery, $project,true);
    // $data =$db->FetchAll(DbTable::ClassPost);
    $Posts =array();
    // $count = 0;
    foreach($data as $post){
        // print($post);
        $count++;
        $obj=[
            '_id'=> (string)$post['_id'],
            'title'=>$post['title'],
            'description'=>$post['description'],
            'quiz'=>($post['quiz']=='NULL')? False:(string)$post['quiz'],
            'postedDate'=>date("d/m/Y H:i:s",(int)$post['postedDate']),
        ];
        array_push($Posts, $obj);
    }

    $returnObj = $Posts;
}catch(Exception $ex){
    $returnObj= $ex->getMessage();
}

var_dump($returnObj);
// print($count);
