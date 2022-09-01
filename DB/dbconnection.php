<?php
require "../vendor/autoload.php";

// header("Content-Type: application/json");

use MongoDB\Driver\Manager;


abstract class DbTable{
    const Users         = 1,
        Quiz            = 2,
        QuizQuestions   = 3,
        PostedQuestions = 4,
        Comments        = 5,
        Classroom       = 6,
        Enroll          = 7,
        CompletedQuiz   = 8,
        ClassPost       = 9;
}

class DataBase{
    public $client, $database, $user;
    
    function __construct(){
        $this->client = new MongoDB\Client(
            'mongodb+srv://abhay:U9pvvr6c2rtdvuHQ@cluster0.lueuj.mongodb.net/?retryWrites=true&w=majority');
        
        //access database;
        $this->database = $this->client->VisualDS;
        
    }

    function FetchAll(int $Table){
        $collection = $this->fetchTable($Table);
        // $RawData = $collection->find();
        // return $this->formateToArray($RawData);

        return $collection->find();
        
    }

    public function insert(int $Table, array $data){
        $collection = $this->fetchTable($Table);
        $collection->insertOne($data);
    }

    public function findOne(int $Table, array $data): ?object{
        $collection = $this->fetchTable($Table);
        return $collection->findOne($data);
    }

    public function find(int $Table, array $data): ?object{
        $collection = $this->fetchTable($Table);
        return $collection->find($data);
    }

    private function fetchTable(int $table){
        switch ($table) {
            case DbTable::Users:
                return $this->database->Users;
            case DbTable::Quiz:
                return $this->database->Quiz;
            case DbTable::QuizQuestions:
                return $this->database->Quiz_Questions; 
            case DbTable::PostedQuestions:
                return $this->database->PostedQuestions; 
            case DbTable::Comments:
                return $this->database->Comments;
            case DbTable::CompletedQuiz:
                return $this->database->Completed_Quiz;
            case DbTable::Enroll:
                return $this->database->Enroll;
            case DbTable::Classroom:
                return $this->database->Classroom;
            case DbTable::ClassPost:
                return $this->database->Class_Post;
            default:
                return NULL;
        }

    }
    public function aggregation(int $Table, array $operation): ?object{
        $collection=$this->fetchTable($Table);
        return $collection->aggregate($operation);
    }
    public function projection(int $Table, array $data, array $proj_arr, bool $many = false): ?object{
        $collection=$this->fetchTable($Table);
        $projection = ['projection'=> $proj_arr];
        
        return (!$many)? $collection->findOne($data,$projection): $collection->find($data,$projection);
    }

    private function formateToArray($RawData): array{
        $array = array();
        foreach($RawData as $data){
            array_push($array,$data);
        }
        return $array;
    }

}



// $DB = new DataBase();
// // echo $DB->Fetch('visualds');
// $table = 'visualds';
// $array = $DB->Fetch($table);
// echo json_encode($array, JSON_PRETTY_PRINT);