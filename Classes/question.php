<?php
require_once '../DB/dbconnection.php';
require_once '../Classes/operation.php';
require_once '../Classes/Common.php';

require "../vendor/autoload.php"; 
use MongoDB\BSON\ObjectId;

class PostedQuestion extends CommonFunction{
    public ?string $question, $subject, $class_id;

    function __construct(string $class_id, string $question = NULL ,string $subject= NULL ){
        $this->question = $question;
        $this->subject = $subject;
        $this->class_id = $class_id;
    }
    
    function Create(string $user_id): ReturnData{
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

    
    private static function formatArray($RawData):array{
        
        $outer_array = array();
        $inner_obj = new stdClass;

        foreach($RawData as $obj){
            $inner_obj->id = (string)$obj->_id;
            $inner_obj->user_id = (string)$obj->user_id;
            $inner_obj->questipn = $obj->question;
            $inner_obj->subject = $obj->subject;
            $inner_obj->class_id = (string)$obj->class_id;

            array_push($outer_array, $inner_obj);
        }
        return $outer_array;
    }
}