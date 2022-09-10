<?php
require_once "../Classes/SimpleRest.php";
require_once "../Classes/quiz.php";
class QuizHandler extends SimpleRest{
    private $quiz;
    function __construct($class_id,$quiz_topics,$score, $description){
        $this->quiz =new Quiz($class_id,$quiz_topics,$score, $description);
    }

    function createQuiz( $questions, $answers, $correct_index, $explanation){
        
        return $this->get_returnData($this->quiz->create($questions, $answers, $correct_index, $explanation));
    }

    function getQuiz(string $quiz_id){
        return $this->get_returnData($this->quiz->getQuiz($quiz_id));
    }



}