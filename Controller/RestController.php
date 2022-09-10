<?php
header("Content-Type: application/json");
require_once "userHandler.php";
require_once "classRoomHandler.php";
require_once "QuestionHandler.php";
require_once "QuizHandler.php";

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
        $classroom = null;
        switch ($page_operation) {
            case 'Read':
                // ob_start();
                // echo "hello";
                $user_id = $_GET['_id'];
                $classroom =  new ClassRoomHandler($user_id);

                if (isset($_GET['type'])){
                    $response = $classroom->getCreateClass();
                    
                }else {
                    $response = $classroom->getEnrolledclass();
                }

                
                // file_put_contents('./log.txt',ob_get_contents());
                // ob_end_flush();
                break;
            case 'Create':

                $classroom =  new ClassRoomHandler("");
                $user_id = $_POST['user_id'];

                $classname = $_POST['classname'];
                $response =$classroom->createClass($classname, $user_id);
                break;
            default:
                # code...
                break;
        }
        break;
    case 'Class':
        $classroom =null;
        switch ($page_operation) {
            case 'Read':

                $class_id = $_GET['class_id'];
                $classroom =  new ClassRoomHandler("");
                $response = $classroom->getPost($class_id);
                break;

            case 'Create':

                $class_id = $_POST['class_id'];
                $user_id = $_POST['user_id'];
                $description= $_POST['description'];
                $quiz = $_POST['quiz'];
                $title = $_POST['title'];
                $classroom =  new ClassRoomHandler("");
                $response = $classroom->postClass( $title,  $description,  $quiz,  $user_id, $class_id);
                break;
            default:
                # code...
                break;
        }
        break;
    case 'Question'://read class question
        $QuestionHandler =  new QuestionHandler();

        switch ($page_operation) {
            case 'Read':

                $class_id = $_GET['class_id'];
                $response = $QuestionHandler->getClassQuestion($class_id);
                break;
            case 'Create':

                $class_id = $_POST['class_id'];
                $question = $_POST['question'];
                $subject = $_POST['subject'];
                $user_id = $_POST['user_id'];

                $response = $QuestionHandler->postQuestion( $user_id, $class_id, $question, $subject);
                break;
            default:
                # code...
                break;
        }
        break;
    case 'Comment':
        $questionHandler =  new QuestionHandler();
        switch ($page_operation) {
            case 'Read':

                $question_id = $_GET['question_id'];
                $response = $questionHandler->getPostedComments($question_id);
                break;
            case 'Create':

                $question_id = $_POST['question_id'];
                $user_id = $_POST['user_id'];
                $comment = $_POST['comment'];
                
                $response = $questionHandler->postComments($user_id, $comment,$question_id);
                break;
            default:
                # code...
                break;
        }
        break;
    case 'Quiz':
        $quizHandler = null;
        switch ($page_operation) {
            case 'Read':
                $quiz_id = $_GET['quiz_id'];
                $quizHandler = new QuizHandler("","",0, "");
                
                
                
                // $question_id = $_GET['question_id'];
                $response = $quizHandler->getQuiz($quiz_id);
                break;
            case 'Create':

                $quiz_topic = $_POST['quiz_topic'];
                $description = $_POST['description'];
                // $questions = $_POST['questions'];
                // $answers = $_POST['answers'];
                // $correct_index = $_POST['correct_index'];
                // $explanation = $_POST['explanation'];
                $class_id = $_POST['class_id'];
                $score = (int)$_POST['score'];

                $questions = array();
                $answers = array();
                $correct_index = array();
                $explanation = array();

                for ($i = 0; $i < $score; ++$i){
                    array_push($questions, $_POST["question$i"]);
                    array_push($correct_index, (int)$_POST["correctAnswer$i"]);
                    array_push($explanation, $_POST["explanation$i"]);
                    $inner_array = array();
                    array_push($inner_array, $_POST['answers'.$i.'_0']);
                    array_push($inner_array, $_POST['answers'.$i.'_1']);
                    array_push($inner_array, $_POST['answers'.$i.'_2']);
                    array_push($inner_array, $_POST['answers'.$i.'_3']);

                    array_push($answers, $inner_array);
                }


                // ob_start();
                // var_dump($answers);
                // var_dump($questions);
                // // echo $quiz_topic;
                // file_put_contents('./log.txt',ob_get_contents());
                // ob_end_flush();
                $quizHandler = new QuizHandler($class_id,$quiz_topic,$score, $description);
                $response = $quizHandler->createQuiz( $questions, $answers, $correct_index, $explanation );
                break;
            default:
                # code...
                break;
        }
        break;

}
echo $response;