<?php

require_once '../DB/dbconnection.php';
require_once "../Classes/users.php";
require_once "../Classes/operation.php";
require "../vendor/autoload.php";
use MongoDB\BSON\ObjectId;

$user = "6290d1deae77e20b9c79e322";
$doc = 
    [
        [
            '$lookup' => [
                'from' =>'Classroom',
                'localField'=> 'class_id',
                'foreignField'=> '_id',
                'as'=>'classroom',
                // 'pipeline'=> [
                //     ['$match'=>['student_id'=> $user]],
                //     // ['$projection'=>['student_id'=>1]]
                // ]
            ]
        ],
        [
            '$replaceRoot'=>[ 'newRoot'=> [ '$mergeObjects'=> [ [ '$arrayElemAt'=> [ '$classroom', 0 ] ], '$$ROOT' ] ] ]
        ],
        
        [ '$project'=> [ 'classroom'=>0 ] ]
    ];

    $searchQuery=[
            
        "student_id"   => new ObjectId($user)
    ];
try {
    $db = new DataBase();
    $result = $db->aggregation(DbTable::Enroll, $doc);
    // $result= $db->find(DbTable::Enroll, $searchQuery);
    //code...
} catch (Exception $th) {
    //throw $th;
}
echo'<pre>';
// var_dump($result);
$users = array();
foreach ($result as $data){
    // print("data:");
    if ($data['student_id']!= $searchQuery['student_id']) continue;
    if (in_array((string)$data['class_id'],$users)) continue;
    array_push($users, (string)$data['class_id']);

    var_dump($data);
}
echo '</pre>';