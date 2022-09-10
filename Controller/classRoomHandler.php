<?php
require_once "../Classes/SimpleRest.php";
require_once "../Classes/classroom.php";
class ClassRoomHandler extends SimpleRest{
    private $classroom;

    function __construct(String $user_id){
        $this->classroom = new Classroom("","",$this->test_input($user_id));
    }

    function getEnrolledclass(){
        return $this->get_returnData($this->classroom->getEnrolledClassrooms());
    }

    function getPost($id){
        return $this->get_returnData($this->classroom->GetClassPost($id));
    }
    function getCreateClass(){
        return $this->get_returnData($this->classroom->getCreatedClass());

    }
    function createClass(String $classname, String $user_id){
        $this->classroom = new Classroom("",$classname,$user_id);
        return $this->get_returnData($this->classroom->Create());
    }

    function postClass(String $title, String $description, String $quiz, String $userid, String $class_id){

        return $this->get_returnData($this->classroom->createClassPost( $title,  $description,  $quiz,  $userid,  $class_id));

    }

    
}