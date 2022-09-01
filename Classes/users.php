<?php
require_once '../DB/dbconnection.php';
require_once '../Classes/operation.php';
require_once '../Classes/question.php';
require_once '../Classes/Common.php';
require_once '../Classes/server.php';
require "../vendor/autoload.php";
use MongoDB\BSON\ObjectId;

class User  extends CommonFunction{
    private ?string $id, $type, $password;
    public ?string $firstName, $lastName, $email;
    public ?bool $verifyEmail;
    public ?int $exp, $level;

    public function __construct($id= NULL, string $first= NULL, string $last= NULL,bool $verify= NULL, string $email= NULL,  int $level= NULL, int $exp= NULL, string $type = NULL){
        if (isset($id->_id)){
            $this->firstName = $id->firstName;
            $this->lastName = $id->lastName;
            $this->email = $id->email;
            $this->type = $id->type;
            $this->password = $id->password;
            $this->verifyEmail = $id->verifyEmail;
            $this->level = $id->level;
            $this->exp = $id->exp;
            $this->id = $id->_id;
        }
        else if (is_array($id)){
            if (count($id) ==2){
                $this->id = null;
                $this->email = $id[0];
                $this->password = $id[1];
            }else {
                $this->id           = NULL;
                $this->firstName    = $id['first'];
                $this->lastName     = $id['last'];
                $this-> password    = $id['pass'];
                $this->email        = $id['email'];
                $this->type         = $id['type'];
                $this->exp          = 0;
                $this->level        = 0;
                $this->verifyEmail  = false;
            }
        }
        else {
            $this->id = $id;
            $this->firstName = $first;
            $this->lastName = $last;
            $this->email = $email;
            $this->type = $type;
            $this->password = NULL;
            $this->verifyEmail = $verify;
            $this->exp = $exp;
            $this->level = $level;
        }
        
    }

    function getJson(bool $withId = true):string{
        $json = array();
        if ($withId){
            $json['id']= $this->id;
        }

        $json["firstName"]     = $this->firstName;
        $json["lastName"]      = $this->lastName;
        $json["email"]         = $this->email;
        $json["type"]          = $this->type;
        $json["password"]      = $this->password;
        $json["verifyEmail"]   = $this->verifyEmail;
        $json["exp"]           = $this->exp;
        $json["level"]         = $this->level;

        return json_encode($json,JSON_PRETTY_PRINT);
    }

    //getters

    function getId():string{
        return $this->id;
    }

    function isStudent(): bool{
        return strtolower($this->type) == "student";
    }
    function isTeacher(): bool{
        return strtolower($this->type) == "teacher";
    }
    
    //setters
    function setPassword($pass){
        $this->password = password_hash($pass, PASSWORD_DEFAULT);
    }

    function increaseLevel(int $value){
        $this->level += $value;
    }

    function increaseExp(int $value){
        $this->exp += $value;
    }

    function getObject():object{
        return $this;
    }

    function getUserDetails(array $proj_arr){
        $searchQuery=[
            '_id'=> new ObjectId($this->id),
        ];

        try{
            $db = new DataBase();
            $retrieve = $db->projection(DbTable::Users, $searchQuery, $proj_arr);
        }catch(Exception $ex){
            return false;
        }

        if ($retrieve!=null){
            $this->firstName    = (isset($retrieve->firstName))? $retrieve->firstName: NULL;
            $this->lastName     = (isset($retrieve->lastName))? $retrieve->lastName: NULL;
            $this->email        = (isset($retrieve->email))? $retrieve->email: NULL;
            $this->type         = (isset($retrieve->type))? $retrieve->type: NULL;
            $this->password     = (isset($retrieve->password))? $retrieve->password: NULL;
            $this->verifyEmail  = (isset($retrieve->verifyEmail))? $retrieve->verifyEmail: NULL;
            $this->level        = (isset($retrieve->level))? $retrieve->level: NULL;
            $this->exp          = (isset($retrieve->exp))? $retrieve->exp: NULL;

            return true;
        }

        return false;
    }
    function logIn(){
        $returnObj = new ReturnData();
        
        $returnObj->perform = false;
        if ($this->id != NULL) return $returnObj;


        // if email exist the retrieve password and id
        $retrieve = CommonFunction::checkExistIn(DbTable::Users, ['email' => $this->email], ['password'=>1, '_id'=>1, 'type' =>1], true );

        if($retrieve!= NULL && $this->validatePassword($retrieve->password)){
            $returnObj->perform = true;
            $returnObj->data = array(
                "id"=> (string)$retrieve->_id,
                "type"=> (string)$retrieve->type
            );
        }else {
            $returnObj->error= "Error, invalid email or password.";
        }
  

        return $returnObj;
        
    }
    private function getArray():array{
        return array(
            "firstName"	    => $this->firstName,
            "lastName"      => $this->lastName,
            "password"      => password_hash($this->password, PASSWORD_DEFAULT),
            "verifyEmail"	=> false,
            "email"	        => $this->email,
            "exp"	        => 0,
            "level"         => 0,
            "type"          => $this->type
        );
    }
    function Create(): ReturnData{
        $returnObj= new ReturnData();
        $returnObj->operation = Operation::Create;
        $returnObj->perform  = false;

        //check if email is in DB
        if (CommonFunction::checkExistIn(DbTable::Users, ['email'=> $this->email], ['_id'=>1])){
            $returnObj->error = "Email already exists";
            return $returnObj;
        }

        //check if email is valid
        $server = new Server();

        //get domain full url
        $link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] 
                === 'on' ? "https" : "http") . 
                "://" . $_SERVER['HTTP_HOST'] . 
                $_SERVER['REQUEST_URI'];
        $form = "<form action='$link/RestController.php?resource=User' method='PUT'><input value='$this->email' name='email' type='hidden<form'/><input value='here' type='submit'></form>";
        //send email
        $sendEmail = $server->sendMail(message: "An account has been created with this email. Please click on this $form to verify your email.", userEmail: $this->email, subject: "Account Create in VisualDs");
        
        if (!$sendEmail){
            $returnObj->error = $server->getError();
            // $returnObj->error = "Error: Email is invalid, please check email.";
            return $returnObj;
        }


        //add data
        $newData = $this->getArray();

        try{
            $db = new DataBase();
            $db->insert(DbTable::Users, $newData);
            $returnObj->perform= true;
            $returnObj->data ="done";
        }catch(Exception $ex){
            $returnObj->error =  $ex->getMessage();
        }
        return $returnObj; 
    }


    function enRoll(string $class_code): ReturnData{
        $returnObj = new ReturnData();
        $returnObj->perform = false;

        //check userid
        if (!CommonFunction::checkExistIn(DbTable::Users, ["_id"=> new ObjectId($this->id)], ['_id' =>1])) {
            $returnObj->error ="Error: user does not exist.";
            return $returnObj;
        }

        //check classroom
        $classroom = CommonFunction::checkExistIn(DbTable::Classroom, ["code"=> $class_code], ['_id' =>1], true); 
        if ($classroom== null || $classroom == false) {;
            $returnObj->error ="Error: class code'$class_code' does not exist.";
            return $returnObj;
        }
        
        //insert in enroll
        $insert=[
            'class_id'=> $classroom->_id,
            'student_id'=> new ObjectId($this->id),
        ];

        try{
            $db = new DataBase();
            $retrive = $db->insert(DbTable::Enroll, $insert);
            $returnObj->perform = true;
        }catch(Exception $ex){
            $returnObj->error ="Error: cannot enroll student.";
        }

        return $returnObj;
    }

    function postQuestion(string $question, string $subject, string $class_id): ReturnData{
        $returnObj = new ReturnData();
        $returnObj->perform = false;

        //check userid
        if (!CommonFunction::checkExistIn(DbTable::Users, ["_id"=> new ObjectId($this->id)], ['_id' =>1])) {
            $returnObj->error ="Error: user does not exist.";
            return $returnObj;
        }

        //check class id
        if (!CommonFunction::checkExistIn(DbTable::Classroom, ["_id"=> new ObjectId($class_id)], ['_id' =>1])) {
            $returnObj->error ="Error: classroom does not exist.";
            return $returnObj;
        }


        // //insert in enroll
        // $insert=[
        //     'class_id'  => new ObjectId($class_id),
        //     'user_id'   => new ObjectId($this->id),
        //     'question'  => $question,
        //     'subject'   => $subject,
        // ];

        // try{
        //     $db = new DataBase();
        //     $retrive = $db->insert(DbTable::PostedQuestions, $insert);
        //     $returnObj->perform = true;
        // }catch(Exception $ex){
        //     $returnObj->error ="Error: cannot posting question.";
        // }

        // return $returnObj;
        
        $postQu = new PostedQuestion($class_id, $question, $subject);
        
        return $postQu->Create($this->user_id);
    }


    function comment(string $question_id, string  $comment): ReturnData{
        $returnObj = new ReturnData();
        $returnObj->perform = false;

        //check userid
        if (!CommonFunction::checkExistIn(DbTable::Users, ["_id"=> new ObjectId($this->id)], ['_id' =>1])) {
            $returnObj->error ="Error: user does not exist.";
            return $returnObj;
        }

        //check Question
        if (!CommonFunction::checkExistIn(DbTable::PostedQuestions, ["_id"=> new ObjectId($question_id)], ['_id' =>1])) {;
            $returnObj->error ="Error: class id does not exist.";
            return $returnObj;
        }
        $date_timestamp = (new Datetime("now"))->getTimeStamp();

        $commentQuery = [
            'date'          => new MongoDB\BSON\UTCDateTime($date_timestamp*1000),
            'user_id'       => new ObjectId($this->id),
            'comment'       => $comment,
            'question_id'   => new ObjectId($question_id)
        ];

        try{
            $db = new DataBase();
            $db->insert(DbTable::Comments, $commentQuery);
            $returnObj->perform = true;
        }catch(Exception $ex){
            $returnObj->error ="Error: cannot posting comment.";
        }

        return $returnObj;
    }

    
    //PRIVATE FUNTION 

    private function validatePassword($pass): bool{
        // has function
        return password_verify($this->password, $pass);
    }

    function getUserName(String $id): array{
        $result = array();
        $Query = ["_id"=> new ObjectId($id)];
        $projection = ['lastName'=>1, 'firstName'=>1];
        try{
            $db = new DataBase();
            $result =$db->projection(DbTable::Users, $Query,$projection);
        }catch(Exception $ex){
            return [0=>'Error in connecting to server'];
        }

        return[ 
            'firstName'=> $result['firstName'],
            'lastName'=> $result['lastName'],
        ];


    }

    
    
}