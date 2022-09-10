<?php
require_once '../DB/dbconnection.php';
require_once '../Classes/Common.php';

require "../vendor/autoload.php"; 
use MongoDB\BSON\ObjectId;

class Quiz extends CommonFunction{
    public string $quiz_topics, $description, $class_id;
    public int $score;

    function __construct($class_id,$quiz_topics,$score, $description){
        $this->class_id = $class_id;
        $this->quiz_topics = $quiz_topics;
        $this->score = $score;
        $this->description = $description;
    }

    function create( $questions, $answers, $correct_index, $explanation){

        $returnObj= new ReturnData();
        $returnObj->perform  = false;

        // if (empty($user_id) ||empty($comment)||empty($question_id)  ){
        //     $returnObj->error ="Cannot insert empty data";
        //     return $returnObj;
        // }

        $newData = [
            'quiz_topic'       => $this->quiz_topics,
            'description'      => $this->description,
            'score'            => $this->score,
            'class_id'         => new ObjectId($this->class_id),
        ];

        $id = null;
        //post comments
        try{
            $db = new DataBase();
            $db->insert(DbTable::Quiz, $newData);
            $returnObj->perform= true;
            $id = $db->getInsertedId();
        }catch(Exception $ex){
            $returnObj->error =  $ex->getMessage();
        }

        if ($returnObj->perform== true){
            //create quiz

            $newData = array();
            for($i = 0; $i < $this->score; ++$i){
                $inner_array = [
                    'quiz_id' => new ObjectId($id),
                    'quiz_question'=> $questions[$i],
                    'answers'=> $answers[$i],
                    'correct_index'=> $correct_index[$i],
                    'points'=> 1,
                    'explanation'=> $explanation[$i],
                ];
                array_push($newData, $inner_array);
            }
            try{
                $db = new DataBase();
                $db->insertMany(DbTable::QuizQuestions, $newData);
                $returnObj->perform= true;
                $returnObj->data = ['quiz_id'=> (string)$id];
                
            }catch(Exception $ex){
                $returnObj->error =  $ex->getMessage();
            }


        }
        return $returnObj;

        
    }
    function getQuiz($quiz_id){
        $returnObj= new ReturnData();
        $returnObj->perform  = false;

        $searchQuery =[
            'quiz_id'=> new ObjectId($quiz_id)
        ];

        try{
            $db = new DataBase();
            $returnObj->data=$db->find(DbTable::QuizQuestions, $searchQuery);
            $returnObj->perform= true;
        }catch(Exception $ex){
            $returnObj->error =  $ex->getMessage();
        }

        if ($returnObj->perform){
            $array = array();
            foreach($returnObj->data as $quiz){
                $inner_array = [
                    'question'=> $quiz->quiz_question,
                    'answers'=> $quiz->answers,
                    'correctAnswer'=> (int)$quiz->correct_index,
                    'explanation'=> $quiz->explanation,
                    // 'points'=> (int)$quiz->points,
                ];
                array_push($array, $inner_array);
            }
            $returnObj->data = $array;
        }
        return $returnObj;
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