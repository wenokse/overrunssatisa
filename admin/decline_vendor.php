<?php
include 'includes/session.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

if(isset($_POST['id'])){
    $id = $_POST['id'];
    $message = $_POST['message'];
    $status = $_POST['status'];
    
    $conn = $pdo->open();
    
    try {
        $stmt = $conn->prepare("UPDATE users SET status=:status WHERE id=:id");
        $stmt->execute(['status'=>$status, 'id'=>$id]);
        
        $stmt = $conn->prepare("SELECT email, firstname, lastname FROM users WHERE id=:id");
        $stmt->execute(['id'=>$id]);
        $vendor = $stmt->fetch();
        
       
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'overrunssatisa@gmail.com';
        $mail->Password   = 'ahuf cbzv bpph caje';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        
      
        $mail->setFrom('overrunssatisa@gmail.com', 'Overruns Sa Tisa Online Shop');
        $mail->addAddress($vendor['email'], $vendor['firstname'] . ' ' . $vendor['lastname']);
        $mail->isHTML(true);
        $mail->Subject = 'Vendor Application Status Update';
        $emailBody = "
        <html>
        <body>
            <h2>Vendor Application Update</h2>
            <h3>Dear {$vendor['firstname']},</h3>
            <p>We regret to inform you that your vendor application has been declined for the following reason:</p>
            <div style='background-color: #f5f5f5; padding: 15px; margin: 10px 0; border-left: 4px solid #dc3545;'>
                {$message}
            </div>
            <p>If you have any questions or would like to discuss this further, please don't hesitate to contact our support team.</p>
            <br>
            <p>Best regards,<br>
            Overruns Sa Tisa Online Shop Team</p>
        </body>
        </html>
        ";
        
        $mail->Body = $emailBody;
        $mail->AltBody = strip_tags(str_replace(['<br>', '</p>'], ["\n", "\n\n"], $emailBody));

        $mail->send();
        
        $_SESSION['success'] = 'Vendor declined successfully. Notification email has been sent.';
    }
    catch(Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
    
    $pdo->close();
}
else {
    $_SESSION['error'] = 'Select vendor to decline';
}

header('location: vendor');
?>