<?php
/*{"_id":{"$oid":"6290fbedf042076182ad1175"},"quiz_id":{"$oid":"6290fad0f042076182ad1173"},"quiz_question":"If I want to find out if an item is in a queue or a stack - what do I call?","answers":["Contains(item)","IsContains(item)","Peek(item)","Find(item)"],"correct_index":{"$numberInt":"2"},"points":{"$numberInt":"2"},"explanation":"This is an explanation"} */
require_once '../DB/dbconnection.php';
require_once '../Classes/Common.php';
require_once '../Classes/operation.php';
require "../vendor/autoload.php";
use MongoDB\BSON\ObjectId;

class QuizQuestions extends CommonFunction{
    public ?string $quiz_id, $quiz_question, $explanation;
    private string $id;
    private ?int $correct_index, $points;
    private ?array $answers;

    function __construct($id, $quiz_id= NULL, $quiz_question= NULL, $explanation= NULL,$correct_index= NULL,$points= NULL,$answers= NULL){
        $this->id = $id ;
        $this->quiz_id = $quiz_id ;
        $this->quiz_question = $quiz_question ;
        $this->explanation= $explanation;
        $this->correct_index = $correct_index;
        $this->answers = $answers;
        $this->points = $points;
    }
    function getid(): string {
        return $this->id;
    }
    function getAnswer_index(): int {
        return $this->correct_index;
    }
    function getPoints(): int {
        return $this->points;
    }

    function getAnswer_options(): array {
        return $this->answers;
    }

    static function getQuizQuestions($quiz_id): ReturnData{
        $returnObj = new ReturnData();
        $returnObj->perform = false;
        $searchQuery = [
            '_id'=> new ObjectId($quiz_id)
        ];

        //check if quiz exist
        if (!CommonFunction::checkExistIn(DbTable::Quiz,$searchQuery,['_id'=>1])){
            $returnObj->error= "Error: quiz id does not exist";
            return $returnObj;
        }

        $searchQuery = [
            'quiz_id'=> new ObjectId($quiz_id)
        ];
        // find
        $projection = [
            'quiz_id' => 0
        ];
        
        try {
            $db = new DataBase();
            $returnObj->data = $db->projection(DbTable::QuizQuestions,$searchQuery, $projection,true);
        } catch (Exception $th) {
            $returnObj->error= $th->getMessage();
            return  $returnObj;
        }
        if ($returnObj->data != NULL){ 
            $returnObj->perform = true;
            $returnObj->data = QuizQuestions::formatArray($returnObj->data);
        }
        return  $returnObj;

    }

    private static function formatArray($RawData):array{
        
        $outer_array = array();

        foreach($RawData as $obj){
            $inner_obj = new stdClass;
            $inner_obj->id = (string)$obj->_id;
            if(isset($obj->quiz_id)) $inner_obj->quiz_id = (string)$obj->quiz_id;
            $inner_obj->explanation = $obj->explanation;
            $inner_obj->correct_index = $obj->correct_index;
            $inner_obj->answers = (array)$obj->answers;
            $inner_obj->points = $obj->points;

            array_push($outer_array, $inner_obj);
        }
        return $outer_array;
    }


}