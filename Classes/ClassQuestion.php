<?php

require_once '../DB/dbconnection.php';
require_once '../Classes/operation.php';
require_once '../Classes/Common.php';

require "../vendor/autoload.php"; 
use MongoDB\BSON\ObjectId;

class ClassQuestion extends CommonFunction{
    private $class_id = null;
    function __construct($class_id = null){
        $this->class_id = $class_id;

    }

    function fetch(){
        $returnObj= new ReturnData();
        $returnObj->perform  = false;

        $searchQuery = [
            'class_id'  => new ObjectId($this->class_id),
        ];
        
        try{
            $db = new DataBase();
            $returnObj->data =$db->find(DbTable::ClassPost, $searchQuery);
            $returnObj->perform = true;
        }catch(Exception $ex){
            $returnObj->error = $ex->getMessage();
        }
        if ($returnObj->data != null){
            $returnObj->data =$this->formatArray($returnObj->data);
        }
        return $returnObj;
    }

    private static function formatArray($RawData):array{
        
        $outer_array = array();
        $inner_obj = new stdClass;

        foreach($RawData as $obj){
            $inner_obj->id = (string)$obj->_id;
            $inner_obj->user_id = (string)$obj->user_id;
            $inner_obj->title = $obj->title;
            $inner_obj->quiz = $obj->quiz;
            $inner_obj->postedDate = $obj->postedDate;
            $inner_obj->description = $obj->description;
            // $inner_obj->class_id = (string)$obj->class_id;

            array_push($outer_array, $inner_obj);
        }
        return $outer_array;
    }
}