<?php

require_once '../vendor/autoload.php';
 $client = new MongoDB\Client(
    'mongodb+srv://abhay:U9pvvr6c2rtdvuHQ@cluster0.lueuj.mongodb.net/?retryWrites=true&w=majority');
    require "../vendor/autoload.php";
    use MongoDB\BSON\ObjectId;
//access database;
$database = $client->VisualDS;

$collection = $database->Quiz_Questions;
// $ops =array( 
//     '$group'=>array(
//         '_id'=> null,
//         'count'=> array('$count'=> array())
//     )
// );
// $ops =array( 
//     '$lookup'=>array(
//         array('from'=> 'Users'),
//         array('localField'=> 'user_id'),
//         array('foreignFeild'=> '_id'),
//         array('as' => 'data')
//     )
// );

$qu_id = new ObjectId('631c8d97b453fd8f9a0f4148');
$doc = $collection->find(['quiz_id'=> $qu_id]);
/*$doc = $collection->aggregate(
    [
        [
            '$match'=>[
                '_id'=> $qu_id
            ]
        ],
        [
            '$lookup' => [
                'from' =>'Quiz_Questions',
                'localField'=> '_id',
                'foreignField'=> 'quiz_id',
                'as'=>'data',
                // 'pipeline'=> [
                //     // [
                //     //     '$match'=>[ '$expr'=>['$eq'=>['question_id',$qu_id]]]
                //     // ],
                //     [
                //         '$project'=>['comment'=>1, 'firstName'=> 1, 'lastName'=> 1]
                //     ]
                // ]
                
            ]
        ],
        [
            '$replaceRoot'=>[ 'newRoot'=> [ '$mergeObjects'=> [ [ '$arrayElemAt'=> [ '$data', 0 ] ], '$$ROOT' ] ] ]
        ],
        
        [ '$project'=> [ 'data'=>0 ] ]
    ]
);*/
// var_dump($doc);
echo"<pre>";
foreach ($doc as $d){
    // var_dump($d->data[0]->_id);
    // $id = ($d->_id);
    // if (is_string($id)){
    //     echo "string<br>";
    // }
    // var_dump($d);
    // echo ('\''.$d->firstName.' '.$d->lastName.'\' userid:'.$d->user_id.'=> '.$d->comment.PHP_EOL);
    var_dump($d);
}
echo"</pre>";

$time = time();

echo date("d/m/Y H:i:s",(int)$time);

