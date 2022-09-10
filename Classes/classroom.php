<?php
require_once '../DB/dbconnection.php';
require_once "../Classes/users.php";
require_once "../Classes/operation.php";
require "../vendor/autoload.php";
use MongoDB\BSON\ObjectId;

class Classroom extends CommonFunction{
    private string $code;
    public string $className;
    public User $user;

    function __construct(string $code, string $className, string $user_id){
        $this->code= $code;
        $this->className= $className;
        $this->user= new User($user_id);
    }
    function getCreatedClass(){
        $returnObj = new ReturnData();
        $returnObj->perform = false;

        $searchQuery = [
            'teacher_id'  => new ObjectId($this->user->getId()),
        ];

        $project=["teacher_id"=>0];

        $data = null;
        try{
            $db = new DataBase();
            $data =$db->projection(DbTable::Classroom, $searchQuery, $project, true);
    
            $returnObj->perform = true;
            $Class =array();

            foreach($data as $class){

                $obj=[
                    '_id'=> (string)$class->code,
                    'classroom_name'=> $class->classroom_name,
                    'class_id'=> (string)$class->_id,
                ];
                array_push($Class, $obj);
            }
        
            $returnObj->data = $Class;
        }catch(Exception $ex){
            $returnObj->error = $ex->getMessage();
        }

        return $returnObj;
    }
    function Create():ReturnData{
        $this->user->getUserDetails(['type' =>1]);
        $returnObj = new ReturnData();
        $returnObj->perform = false;

        if (!$this->user->isTeacher()){
            $returnObj->error="Error user is not a user";
            return $returnObj;
        }
        
        do {
            $this->generate_code();//get new code
        }while (CommonFunction::checkExistIn(DbTable::Classroom,['code'=> $this->code], ['code'=>1]));
        
        $insertQuery=[
            "classroom_name"    => $this->className,
            "code"              => $this->code,
            "teacher_id"        => new ObjectId($this->user->getId())
        ];

        try{
            $db = new DataBase();
            $db->insert(DbTable::Classroom, $insertQuery);
            $returnObj->data = ['code' => $this->code];
            $returnObj->perform = true;
        }catch(Exception $ex){
            $returnObj->error ="Error: cannot create classroom.";
        }
        return $returnObj;// return  the code of the classroom in data attribute
    }
    function createClassPost(String $title, String $description, String $quiz, String $userid, String $class_id){
        $returnObj = new ReturnData();
        $returnObj->perform = false;

        $newdata = [
            'title'=> $title,
            'description'=> $description,
            'quiz'=> $quiz,
            'postedDate' => new MongoDB\BSON\Timestamp(1,time()),
            'class_id'  => new ObjectId($class_id),
            'user_id'  => new ObjectId($class_id),
        ];
        
        $data = null;
        try{
            $db = new DataBase();
            $data =$db->insert(DbTable::ClassPost, $newdata);
            $returnObj->perform = true;
        }catch(Exception $ex){
            $returnObj->error = $ex->getMessage();
        }

        return $returnObj;
    }
    function GetClassPost(String $class_id){
        $returnObj = new ReturnData();
        $returnObj->perform = false;

        $searchQuery = [
            'class_id'  => new ObjectId($class_id),
        ];
        $project=[
            'class_id'  => 0
        ];
        $data = null;
        try{
            $db = new DataBase();
            $data =$db->projection(DbTable::ClassPost, $searchQuery, $project,true);
            // $data =$db->FetchAll(DbTable::ClassPost);
            $returnObj->perform = true;
            $Posts =array();

            foreach($data as $post){

                $obj=[
                    '_id'=> (string)$post['_id'],
                    'title'=>$post['title'],
                    'description'=>$post['description'],
                    'quiz'=>($post['quiz']=='NULL')? False:(string)$post['quiz'],
                    'postedDate'=>date("d/m/Y H:i:s",(int)$post['postedDate']->getTimestamp()),
                ];
                array_push($Posts, $obj);
            }
        
            $returnObj->data = $Posts;
        }catch(Exception $ex){
            $returnObj->error = $ex->getMessage();
        }

        return $returnObj;
    }
    function getEnrolledClassrooms(){
        $returnObj = new ReturnData();
        $returnObj->perform = false;


        $doc = 
        [
            [
                '$lookup' => [
                    'from' =>'Classroom',
                    'localField'=> 'class_id',
                    'foreignField'=> '_id',
                    'as'=>'classroom',
                    // 'pipeline'=> [['$match'=>['$eq'=>['$student_id', $user]]]]
                    // 'pipeline'=> [
                    //     ['$match'=>['student_id'=> $this->user->getId()]]
                    // ]
                ]
            ],
            [
                '$replaceRoot'=>[ 'newRoot'=> [ '$mergeObjects'=> [ [ '$arrayElemAt'=> [ '$classroom', 0 ] ], '$$ROOT' ] ] ]
            ],
            
            [ '$project'=> [ 'classroom'=>0 ] ]
        ];
        $searchQuery=[
            
            "student_id"   => new ObjectId($this->user->getId())
        ];


        try{
            $db = new DataBase();
            $result = $db->aggregation(DbTable::Enroll, $doc);

            $class = array();
            foreach ($result as $data){
                if ($data['student_id']!= $searchQuery['student_id']) continue;
                //get teacher name 
                $name = $this->user->getUserName($data->teacher_id);
                // array_push($data, $name);
                $data['name'] = $name['firstName'].' '. $name['lastName'];
                //add to array
                array_push($class, $data);
            }
            // var_dump($class);
            $returnObj->data = $this->formateClass($class);
            $returnObj->perform = true;
        }catch(Exception $ex){
            $returnObj->error ="Error: cannot search for enrolled class.";
        }
        return $returnObj;// return  the code of the classroom in data attribute
        
    }
    private function generate_code()
    {
        $length_of_string = 6;
        // String of all alphanumeric character
        //$str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
     
        // Shuffle the $str_result and returns substring
        // of specified length
        //$this->code = substr(str_shuffle($str_result), 0, $length_of_string);

        $this->code =substr(sha1(time()), 0, $length_of_string);
    }

    private function formateClass(array $classes): array{
        $returnClass = array();
        $classIdArr = array();
        // $count=0;
        foreach($classes as $class){
            // $count++;
            $objClass = [
                '_id'=> (string)$class->code,
                'classroom_name'=> $class->classroom_name,
                'class_id'=> (string)$class->class_id,
                'teacher_id'=> (string)$class->teacher_id,
                'teacher_name'=> $class->name,
            ];
            //remove duplicates
            if (in_array($objClass['class_id'], $classIdArr)) continue;
            array_push($classIdArr, $objClass['class_id']);
            
            array_push($returnClass, $objClass);
        }
        // ob_start();
        // file_put_contents('./log.txt',ob_get_contents());
        // // echo ($count);
        // // var_dump($returnClass);
        // ob_end_flush();
        return $returnClass;
    }
}