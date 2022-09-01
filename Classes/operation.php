<?php
abstract class Operation{
    const Create = -1, Read =-2, Update=-3, Delete=-4, Insert=-5;

}
class ReturnData{
    public ?bool $perform;
    public int $operation;
    public ?string $error;
    public $data;
    
    public function __construct(){
        $this->operation = 0;
        $this->perform = NULL;
        $this->error    = NULL;
        $this->data = NUll;
    }
}