<?php
require_once '../DB/dbconnection.php';
require_once '../Classes/operation.php';
require_once '../Classes/Common.php';

require "../vendor/autoload.php"; 
use MongoDB\BSON\ObjectId;

class PostedQuestion extends CommonFunction{
    public ?string $question, $subject, $class_id;

    function __construct(string $class_id= null, string $question = NULL ,string $subject= NULL ){
        $this->question = $question;
        $this->subject = $subject;
        $this->class_id = $class_id;
    }
    function createComment(String $user_id, String $comment, String $question_id){
        $returnObj= new ReturnData();
        $returnObj->perform  = false;

        if (empty($user_id) ||empty($comment)||empty($question_id)  ){
            $returnObj->error ="Cannot insert empty data";
            return $returnObj;
        }

        $newData = [
            'date'         => (int)time(),
            'user_id'      => new ObjectId($user_id),
            'comment'      => $comment,
            'question_id'  => new ObjectId($question_id),
        ];


        //post comments
        try{
            $db = new DataBase();
            $db->insert(DbTable::Comments, $newData);
            $returnObj->perform= true;
            $returnObj->data = true;
        }catch(Exception $ex){
            $returnObj->error =  $ex->getMessage();
        }

        return $returnObj;

    }
    function createQuestion(string $user_id): ReturnData{
        $returnObj= new ReturnData();
        $returnObj->perform  = false;
        if (empty($this->question) ||empty($this->subject)  ){
            $returnObj->error ="Cannot insert empty data";
            return $returnObj;
        }

        $newData = [
            'user_id'   => new ObjectId($user_id),
            'question'  => $this->question,
            'subject'   => $this->subject,
            'class_id'  => new ObjectId($this->class_id),
        ];

        try{
            $db = new DataBase();
            $db->insert(DbTable::PostedQuestions, $newData);
            $returnObj->perform= true;
        }catch(Exception $ex){
            $returnObj->error =  $ex->getMessage();
        }
        return $returnObj;
    }

    function fetch(){
        $returnObj= new ReturnData();
        $returnObj->perform  = false;

        $searchQuery = [
            'class_id'  => new ObjectId($this->class_id),
        ];
        
        try{
            $db = new DataBase();
            $returnObj->data =$db->find(DbTable::PostedQuestions, $searchQuery);
            $returnObj->perform = true;
        }catch(Exception $ex){
            $returnObj->error = $ex->getMessage();
        }
        if ($returnObj->data != null){
            $returnObj->data =PostedQuestion::formatArray($returnObj->data);
        }
        return $returnObj;
    }

    public function fetchComment(String $question_id){
        $returnObj= new ReturnData();
        $returnObj->perform  = false;
        
        $aggregation_query = 
        [
            [
                '$match'=>[
                    'question_id'=> new ObjectId($question_id)
                ]
            ],
            [
                '$lookup' => [
                    'from' =>'Users',
                    'localField'=> 'user_id',
                    'foreignField'=> '_id',
                    'as'=>'data',
                    'pipeline'=> [
                        [
                            '$project'=>['comment'=>1, 'firstName'=> 1, 'lastName'=> 1]
                        ]
                    ]
                    
                ]
            ],
            [
                '$replaceRoot'=>[ 'newRoot'=> [ '$mergeObjects'=> [ [ '$arrayElemAt'=> [ '$data', 0 ] ], '$$ROOT' ] ] ]
            ],
            
            [ '$project'=> [ 'data'=>0 ] ]
        ];
        try{
            $db = new DataBase();
            $returnObj->data =$db->aggregation(DbTable::Comments, $aggregation_query);
            $returnObj->perform = true;
        }catch(Exception $ex){
            $returnObj->error = $ex->getMessage();
        }

        if ($returnObj->data != null){
            $inner_array= array();
            $outer_array= array();
            foreach($returnObj->data as $obj){
                $inner_array =[
                'user_id'=> (String) $obj->user_id,
                'username'=> $obj->firstName.' '.$obj->lastName,
                'comment'=> $obj->comment,
                ]; 
                
                array_push($outer_array,$inner_array);
            }
            $returnObj->data = $outer_array;
        }

        return $returnObj;
    }
    
    private static function formatArray($RawData):array{
        
        $outer_array = array();
        $inner_obj = array();
        $classIdArr= array();
        foreach($RawData as $obj){

            $inner_obj['id'] = (string)$obj->_id;
            $inner_obj['user_id'] = (string)$obj->user_id;
            $inner_obj['question'] = $obj->question;
            $inner_obj['subject'] = $obj->subject;
            $inner_obj['class_id'] = (string)$obj->class_id;
            
            // if (in_array((string)$inner_obj['id'], $classIdArr)) continue;
            // array_push($classIdArr, (string)$inner_obj['id']);
            
            array_push($outer_array, $inner_obj);
        }
        return $outer_array;
    }
}