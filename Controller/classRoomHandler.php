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
}