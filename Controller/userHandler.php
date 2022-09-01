<?php
require_once "../Classes/users.php";
require_once "../Classes/SimpleRest.php";
class UserHandler extends SimpleRest{
    private $user;

    function __construct(){
        $user = new User();
    }

    function signIn(array $new_user){
        $user = new User($new_user);
        $operation =$user->Create();
        return $this->get_returnData($operation);
    }

    function logIn(string $email, string $password){
        $user = new User([$this->test_input($email), $this->test_input($password)]);
        return $this->get_returnData($user->logIn());
    }
    function enrollIn(string $class_code, string $student_id){
        $class_code = $this->test_input($class_code);
        $student_id = $this->test_input($student_id);

        if (strlen($class_code)!= 6) return $this->get_returnData(NULL);

        $user = new User($student_id);
        return $this->get_returnData($user->enRoll($class_code));
    }

    function postQuestion(string $user_id, string $class_id, string $subject,string $question ){
        $question = $this->test_input($question);
        $subject = $this->test_input($subject);
        $user_id = $this->test_input($user_id);
        $class_id = $this->test_input($class_id);

        $user = new User($user_id);
        return $this->get_returnData($user->postQuestion($question, $subject, $class_id));
    }

    function comment(string $user_id, string $question_id, string $comment ){
        $comment = $this->test_input($comment);
        $question_id = $this->test_input($question_id);
        $user_id = $this->test_input($user_id);
       
        $user = new User($user_id);
        return $this->get_returnData($user->comment($question_id, $comment));
    }



}