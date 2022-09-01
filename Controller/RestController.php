<?php
header("Content-Type: application/json");
require_once "userHandler.php";
require_once "classRoomHandler.php";

if(isset($_GET["resource"]))
	$resource = $_GET["resource"];
else {

    echo json_encode(array("success" => 0));
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

$page_operation = "Read";//get
if ($method === "PUT")
    $page_operation = "Update";
else if ($method === "POST")
    $page_operation = "Create";
else if ($method === "DELETE")
    $page_operation = "Delete";

$response = null;

switch ($resource){
    case 'User':
        $user = new UserHandler();

        switch ($page_operation) {
            case 'Read':
                $data = $_GET;
            
                break;
            case 'Create':
                $data = $_POST;
                if (count($_POST) ==2){
                   $response = $user->logIn( $data['email'],$data['password']);
                }else{
 
                    $response =$user->signIn($data);
                } 
        
                break;
            case 'Update':

// ob_start();
                parse_str(file_get_contents("php://input"),$data);
                // $data = $_REQUEST;
                // var_dump($data);
                // file_put_contents('./log.txt',ob_get_contents());
                // ob_end_flush();
                $response =$user->enrollIn($data['code'], $data['_id']);
            default:
                # code...
                break;
        }
        break;
    case 'Classroom':
        $classroom =null;
        switch ($page_operation) {
            case 'Read':
// ob_start();
                // echo "hello";
                $user_id = $_GET['_id'];
                $classroom =  new ClassRoomHandler($user_id);
                $response = $classroom->getEnrolledclass();
                
                // file_put_contents('./log.txt',ob_get_contents());
                // ob_end_flush();
                break;
            default:
                # code...
                break;
        }
        break;
    case 'Class':
        $classroom =null;
        switch ($page_operation) {
            case 'Create':

                $class_id = $_POST['class_id'];
                $classroom =  new ClassRoomHandler("");
                $response = $classroom->getPost($class_id);
                break;
            default:
                # code...
                break;
        }
        break;
}
echo $response;