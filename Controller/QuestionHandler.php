<?php
require_once "../Classes/SimpleRest.php";
require_once "../Classes/ClassQuestion.php";
class QuestionHandler extends SimpleRest{
    private $question;
    function __construct(){
        $this->question =new PostedQuestion();
    }

    function getClassQuestion(String $class_id){
        $this->question= new PostedQuestion($class_id);
        return $this->get_returnData($this->question->fetch());
    }

    function getPostedComments(String $question_id){
        return $this->get_returnData($this->question->fetchComment($question_id));
    }

    function postComments(String $user_id, String $comment, String $question_id){
        return $this->get_returnData($this->question->createComment($user_id, $comment, $question_id));
    }

    function postQuestion(String $user_id, String $class_id, String $question, String $subject){
        $this->question= new PostedQuestion($class_id, $question, $subject);

        return $this->get_returnData($this->question->createQuestion($user_id));
    }


}