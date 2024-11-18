<?php
include 'includes/session.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize user input
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email address.";
        header('location: password_forgot');
        exit();
    }

    // Generate a 6-digit OTP
    $otp = sprintf("%06d", mt_rand(0, 999999)); 
    $otp_hash = password_hash($otp, PASSWORD_DEFAULT);
    $expiry = time() + 600; // 10 minutes from now

    try {
        $conn = $pdo->open();
        $sql = "UPDATE users
        SET reset_code = :reset_code, reset_code_expiry = :expiry
        WHERE email = :email";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':reset_code', $otp);
        $stmt->bindParam(':expiry', $expiry);
        $stmt->bindParam(':email', $email);

        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                // Send email
                $mail = new PHPMailer(true);
                try {
                    //Server settings
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
                    $mail->Timeout = 600; 
                    $mail->SMTPKeepAlive = true;
            
                    //Recipients
                    $mail->setFrom('overrunssatisa@gmail.com', 'Overruns Sa Tisa Online Shop');
                    $mail->addAddress($email);

                    //Content
                    $mail->isHTML(true);
                    $mail->Subject = 'Password Reset OTP';
                    $mail->Body    = "Your OTP for password reset is: <strong>$otp</strong>  . It will expire in 10 minutes.";

                    $mail->send();
                    $_SESSION['success'] = 'OTP sent to your email. Please check your inbox.';
                    $_SESSION['reset_email'] = $email;
                    $_SESSION['reset_time'] = $expiry;
                    header('location: reset_verify');
                    exit();
                } catch (Exception $e) {
                    $_SESSION['error'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                    error_log("Email sending failed: " . $e->getMessage());
                }
            } else {
                $_SESSION['error'] = 'Email not found in our records.';
            }
        } else {
            $_SESSION['error'] = "Failed to update database.";
        }
    } catch(PDOException $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
    }

    $pdo->close();
    header('location: password_forgot');
    exit();
}
?>
<?php include 'includes/header.php'; ?>
<script src="js/sweetalert.min.js"></script>
<body>
<br><br><br><br>
<div class="container2">
<a href="login" style="color: rgb(0, 51, 102); "><i class="fa fa-arrow-left" style="color: rgb(0, 51, 102);"></i></a></p>
    <center> <h2>Forgot Password</h2></center><br>
   
    <?php
  if (isset($_SESSION['error']) || isset($_SESSION['success'])) {
    $message = isset($_SESSION['error']) ? $_SESSION['error'] : $_SESSION['success'];
    $icon = isset($_SESSION['error']) ? 'error' : 'success';
    echo "
      <script>
        swal({
          title: '". $message ."',
          icon: '". $icon ."',
          button: 'OK'
        });
      </script>
    ";
    unset($_SESSION['error']);
    unset($_SESSION['success']);
  }
?>
    <form action="password_forgot" method="POST">
        <input type="email" id="email" name="email" placeholder="Enter Your Email" required><br>
        <button type="submit" class="btn btn-primary btn-block ">Send OTP</button>
    </form>
    <a href = "another" class = "button">Use Another Way</a>
</div>

<style>
  body{
    background: rgb(0, 51, 102);
    background-size: cover;
    background-repeat: no-repeat;
  }
  .container2 { 
    width: 500px;
    height: 260px;
    margin: 0 auto 50px;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 10px;
    background-color: #f9f9f9;
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
    align: center;
    
  }
  .container2 input{
    background-color: #eee;
    border: none;
    margin: 8px 0;
    padding: 10px 15px;
    font-size: 13px;
    border-radius: 8px;
    width: 100%;
    outline: none;
}
.container2 button{
    background-color: #512da8;
    color: #fff;
    font-size: 12px;
    padding: 10px 45px;
    border: 1px solid transparent;
    border-radius: 20px;
    font-weight: 600;
    letter-spacing: 0.5px;
    text-transform: uppercase;
   
    cursor: pointer;
}

  
  </style>
</body>
</html>

