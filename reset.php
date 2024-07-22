<?php
include 'includes/session.php';

try {
    if(isset($_POST['email'])){
        $email = $_POST['email'];

        $conn = $pdo->open();

        $stmt = $conn->prepare("SELECT *, COUNT(*) AS numrows FROM users WHERE email=:email");
        $stmt->execute(['email'=>$email]);
        $row = $stmt->fetch();

        if($row['numrows'] > 0){
            // Generate token
            $token = bin2hex(random_bytes(16));
            $token_hash = hash("sha256", $token);
            $expiry = date("Y-m-d H:i:s", time() + 60 * 30); // 30 minutes from now

            // Update user with reset token
            $stmt = $conn->prepare("UPDATE users SET reset_token_hash=:token, reset_token_expires_at=:expiry WHERE email=:email");
            $stmt->execute(['token'=>$token_hash, 'expiry'=>$expiry, 'email'=>$email]);

            // Send email
            $mail = require __DIR__ . "/mailer.php";
            $mail->setFrom("overrunssatisa@gmail.com");
            $mail->addAddress($email);
            $mail->Subject = "Password Reset";
            $mail->isHTML(true);
            $mail->Body = <<<END
            Click <a href="http://localhost/overrunssatisa/password_reset.php?token=$token">here</a> 
            to reset your password.
            END;

            try {
                $mail->send();
                $_SESSION['success'] = 'Message sent, please check your inbox.';
            } catch (Exception $e) {
                throw new Exception("Message could not be sent. Mailer error: {$mail->ErrorInfo}");
            }
        }
        else{
            $_SESSION['error'] = 'Email not found';
        }

        $pdo->close();
    }
    else{
        $_SESSION['error'] = 'Input email associated with account';
    }
} catch (Exception $e) {
    $_SESSION['error'] = 'An error occurred: ' . $e->getMessage();
}

header('location: password_forgot.php');