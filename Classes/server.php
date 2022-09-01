<?php
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader

class Server{
    private $mail = null;
    private string $serverUser, $serverPass, $serverError;

    public function __construct(){
        $this->mail =new PHPMailer(true);
        $this->serverUser = "fakeservermailsender@gmail.com";
        $this->serverPass = "rjzmieuxenwkfrzs";
        $this->serverError = "";
    }
    
    function getError():string{
        return $this->serverError;
    }
    function sendMail(string $message, string $userEmail, string $subject):bool{

        try {
            //Server settings
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
            $this->mail->isSMTP();                                            // Send using SMTP
            $this->mail->Host       = 'smtp.gmail.com';                     // Set the SMTP server to send through
            $this->mail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $this->mail->Username   = $this->serverUser;       // SMTP username
            $this->mail->Password   = $this->serverPass;                      // SMTP password
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
            $this->mail->Port       = 465;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
        
            //Recipients
            $this->mail->setFrom('dharesh.bandhu@umail.uom.ac.mu', "Mailer");
            $this->mail->addAddress($userEmail);    // Add a recipient
            // $mail->addReplyTo('chandrashekhar.bechoo@umail.uom.ac.mu', 'Information');
            /*
            $mail->addCC('dharesh.bandhu@umail.uom.ac.mu');
            $mail->addBCC('chandrashekhar.bechoo@umail.uom.ac.mu');*/
        
        
            // Content
            $this->mail->isHTML(true);                                  // Set email format to HTML
            $this->mail->Subject = $subject;
            $this->mail->Body    = $message;
            //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
        
            $this->mail->send();
            $this->serverError = "";
            return true;
        } catch (Exception $e) {
            // echo $e->getMessage();
            $this->serverError =$e->getMessage();

            return false;
        }
    }



}
