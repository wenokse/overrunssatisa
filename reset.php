<?php
include 'includes/session.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Sanitize user input
$email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = "Invalid email address.";
    header('location: password_forgot.php');
    exit();
}

$otp = sprintf("%06d", mt_rand(0, 999999)); // Generate a 6-digit OTP
$otp_hash = password_hash($otp, PASSWORD_DEFAULT);
$expiry = date("Y-m-d H:i:s", time() + 60 * 5);

$conn = $pdo->open();
$sql = "UPDATE users
        SET reset_code = :reset_code, created_on = :expiry
        WHERE email = :email";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':reset_code', $otp_hash);
$stmt->bindParam(':expiry', $expiry);
$stmt->bindParam(':email', $email);

if ($stmt->execute()) {
    if ($stmt->rowCount() > 0) {
        // Send email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username   = 'overrunssatisa@gmail.com';
            $mail->Password   = 'ahuf cbzv bpph caje';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; 
            $mail->Port = 465;
        
            $mail->setFrom('overrunssatisa@gmail.com', 'Overruns Sa Tisa Online Shop');
            $mail->addAddress($email);
        
            $mail->Subject = "Password Reset OTP";
            $mail->isHTML(true);
            $mail->Body = "Your OTP for password reset is: <strong>$otp</strong>. It will expire in 5 minutes.";
        
            $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Enable debugging
        
            $mail->send();
            $_SESSION['success'] = 'OTP sent to your email. Please check your inbox.';
        } catch (Exception $e) {
            $_SESSION['error'] = "Message could not be sent. Mailer error: {$mail->ErrorInfo}";
            error_log("Email sending failed: " . $e->getMessage());
        }
        
    } else {
        $_SESSION['error'] = 'Email not found';
    }
} else {
    $_SESSION['error'] = "Failed to execute query.";
}

$pdo->close();
header('location: password_forgot.php');

function sendSMS($email, $otp) {
    // Implement your SMS sending logic here
}
?>
