<?php

include 'includes/session.php';

$email = $_POST["email"];


$token = bin2hex(random_bytes(16));
$token_hash = hash("sha256", $token);


$expiry = date("Y-m-d H:i:s", time() + 60 * 5);
$conn = $pdo->open();

$sql = "UPDATE users
        SET reset_token_hash = :reset_token_hash,
            reset_token_expires_at = :reset_token_expires_at
        WHERE email = :email";

$stmt = $conn->prepare($sql);

// Bind parameters
$stmt->bindParam(':reset_token_hash', $token_hash);
$stmt->bindParam(':reset_token_expires_at', $expiry);
$stmt->bindParam(':email', $email);

if ($stmt->execute()) {
   
    if ($stmt->rowCount() > 0) {

     
        $mail = require __DIR__ . "/mailer.php";

        $mail->setFrom("overrunssatisa@gmail.com");
        $mail->addAddress($email);
        $mail->Subject = "Password Reset";
        $mail->isHTML(true);
        $mail->Body = <<<END
        Click <a href="http://localhost/OverrunsSaTisaShopSystem123/password_reset.php?token=$token">here</a> 
        to reset your password.
        END;

        try {
            $mail->send();
            $_SESSION['success'] = 'Message sent, please check your inbox.';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer error: {$mail->ErrorInfo}";
        }
    } else {
        $_SESSION['error'] = 'Email not found';
    }
} else {
    echo "Failed to execute query.";
}


$pdo->close();
header('location: password_forgot.php');
?>

