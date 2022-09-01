<?php
require_once '../DB/dbconnection.php';
require_once '../Classes/Common.php';


class Quiz extends CommonFunction{
    public string $quiz_topics, $description, $class_id;
    public int $score;

    function __construct($class_id,$quiz_topics,$score, $description){
        $this->class_id = $class_id;
        $this->quiz_topics = $quiz_topics;
        $this->score = $score;
        $this->description = $description;
    }

    static function getQuestions($quiz_id){
        
    }

    static function fetchAll(){
        $db = new DataBase();
        return Quiz::formatArray($db->fetchAll(DbTable::Quiz));
    }

    private static function formatArray($RawData):array{

        $outer_array = array();
        $inner_obj = new stdClass;

        foreach($RawData as $obj){
            $inner_obj->id = (string)$obj->_id;
            $inner_obj->quiz_topics = (string)$obj->quiz_topics;
            $inner_obj->description = (string)$obj->description;
            $inner_obj->score = $obj->score;
            $inner_obj->class_id = (string)$obj->class_id;

            array_push($outer_array, $inner_obj);
        }
        return $outer_array;
    }

}