<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . "/vendor/autoload.php";

$mail = new PHPMailer(true);


$mail->isSMTP();
$mail->Host = "smtp.gmail.com";
$mail->SMTPAuth = true;
$mail->Username = "overrunssatisa@gmail.com";
$mail->Password = "fjaesiyllqeynngx"; 
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = 587;

$mail->isHtml(true);

return $mail;