
<?php 
/*
A simple RESTful webservices base class
Use this as a template and build upon it
Reference : https://phppot.com/php/php-restful-web-service/
*/
require_once '../Classes/operation.php';
class SimpleRest {
	
	private $httpVersion = "HTTP/1.1";

	public function setHttpHeaders($contentType, $statusCode){
		
		$statusMessage = $this -> getHttpStatusMessage($statusCode);
		
		header($this->httpVersion. " ". $statusCode ." ". $statusMessage);	
		header("Content-Type:". $contentType);
	}
	
	public function getHttpStatusMessage($statusCode){
		$httpStatus = array(
			100 => 'Continue',  
			101 => 'Switching Protocols',  
			200 => 'OK',
			201 => 'Created',  
			202 => 'Accepted',  
			203 => 'Non-Authoritative Information',  
			204 => 'No Content',  
			205 => 'Reset Content',  
			206 => 'Partial Content',  
			300 => 'Multiple Choices',  
			301 => 'Moved Permanently',  
			302 => 'Found',  
			303 => 'See Other',  
			304 => 'Not Modified',  
			305 => 'Use Proxy',  
			306 => '(Unused)',  
			307 => 'Temporary Redirect',  
			400 => 'Bad Request',  
			401 => 'Unauthorized',  
			402 => 'Payment Required',  
			403 => 'Forbidden',  
			404 => 'Not Found',  
			405 => 'Method Not Allowed',  
			406 => 'Not Acceptable',  
			407 => 'Proxy Authentication Required',  
			408 => 'Request Timeout',  
			409 => 'Conflict',  
			410 => 'Gone',  
			411 => 'Length Required',  
			412 => 'Precondition Failed',  
			413 => 'Request Entity Too Large',  
			414 => 'Request-URI Too Long',  
			415 => 'Unsupported Media Type',  
			416 => 'Requested Range Not Satisfiable',  
			417 => 'Expectation Failed',  
			500 => 'Internal Server Error',  
			501 => 'Not Implemented',  
			502 => 'Bad Gateway',  
			503 => 'Service Unavailable',  
			504 => 'Gateway Timeout',  
			505 => 'HTTP Version Not Supported');
		return ($httpStatus[$statusCode]) ? $httpStatus[$statusCode] : $status[500];
	}


	private function encodeJson($responseData) {
		$jsonResponse = json_encode($responseData, JSON_PRETTY_PRINT);
		return $jsonResponse;		
	}

    public function get_returnData($data){
        $statusCode = 200;
        $returnData = null;		

        //check fpr error
        if (empty($data) || $data == null){
            $statusCode = 404;
            $returnData = array('success' => 0, "error" => "Error in return data.");			
        }
        elseif(!$data->perform ){
            $statusCode = 404;
            $returnData = array('success' => 0, "error" => $data->error);	
        }else {
            $returnData = array('success' => 1, "data" => $data->data);	
		}
        // else {
        //     $data ["data"] = $data;		
        // }

        // $requestContentType = $_SERVER['HTTP_ACCEPT'];
		// $this->setHttpHeaders($requestContentType , $statusCode);

        $result = $this->encodeJson($returnData);
        return $result;
        // echo $result;
    }

	public function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
}