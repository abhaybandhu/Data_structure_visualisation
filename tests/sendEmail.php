<?php

require "../Classes/server.php";
$server = new Server();
$sendmail= $server->sendMail(message: "This is a test message", userEmail: $_POST['email'], subject: "test");

if ($sendmail) echo "sent";
else echo "not sent Error:". $server->getError();