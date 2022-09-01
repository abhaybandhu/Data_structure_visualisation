<?php

require_once '../vendor/autoload.php';
 $client = new MongoDB\Client(
    'mongodb+srv://abhay:U9pvvr6c2rtdvuHQ@cluster0.lueuj.mongodb.net/?retryWrites=true&w=majority');

//access database;
$database = $client->VisualDS;

$collection = $database->Comments;
$ops =array( 
    '$group'=>array(
        '_id'=> null,
        'count'=> array('$count'=> array())
    )
);
$ops =array( 
    '$lookup'=>array(
        array('from'=> 'Users'),
        array('localField'=> 'user_id'),
        array('foreignFeild'=> '_id'),
        array('as' => 'data')
    )
);


$doc = $collection->aggregate(
    [
        [
            '$lookup' => [
                'from' =>'Users',
                'localField'=> 'user_id',
                'foreignField'=> '_id',
                'as'=>'data'
                
            ]
        ]
    ]
);
// var_dump($doc);
foreach ($doc as $d){
    var_dump($d->data[0]->_id);
    $id = ($d->_id);
    if (is_string($id)){
        echo "string<br>";
    }
}

