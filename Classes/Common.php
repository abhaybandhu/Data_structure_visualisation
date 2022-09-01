<?php
require_once '../DB/dbconnection.php';

abstract class CommonFunction{
    protected static function checkExistIn (int $table, array $searchQuery, array $proj_arr, bool $returnObj = false){

        // $projection = ['projection'=> $proj_arr];
        try{
            $db = new DataBase();
            $retrieve = $db->projection($table, $searchQuery, $proj_arr);
            // var_dump($retrieve);//for debug purpose
            if ($retrieve != Null)return (!$returnObj)? true: $retrieve;
            return (!$returnObj)? false: null;
        }catch(Exception $ex){
            echo"error";
           return (!$returnObj)? false: null;
        }
    }
}