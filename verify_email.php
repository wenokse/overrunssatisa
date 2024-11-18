
<?php
include 'includes/session.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Function to send verification email
function sendVerificationEmail($email, $firstname, $lastname, $verification_code) {
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
        $mail->Timeout = 300;
        $mail->SMTPKeepAlive = true;

        //Recipients
        $mail->setFrom('overrunssatisa@gmail.com', 'Overruns Sa Tisa Online Shop');
        $mail->addAddress($email, $firstname . ' ' . $lastname);

        //Content
        $mail->isHTML(true);
        $mail->Subject = 'Email Verification Code';
        $mail->Body    = "Thank you for registering to our shop; <br>  Your verification code is: <b>$verification_code</b>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Failed to send email. Error: " . $e->getMessage());
        return false;
    }
}

if (isset($_POST['verify'])) {
    $user_code = $_POST['verification_code'];
    $email_code = $_SESSION['temp_user_data']['email_code'];
    $sms_code = $_SESSION['temp_user_data']['sms_code'];
    $code_time = $_SESSION['temp_user_data']['code_time'];

    // Check if the code has expired
    if (time() > $code_time) {
        $_SESSION['error'] = 'Verification code has expired. Please request a new one.';
        header('location: verify_email');
        exit();
    }

    if ($user_code == $email_code || $user_code == $sms_code) {
        // Verification successful, create user
        $conn = $pdo->open();
        $now = date('Y-m-d');
        $password = password_hash($_SESSION['temp_user_data']['password'], PASSWORD_DEFAULT);

        try {
            $stmt = $conn->prepare("INSERT INTO users (email, password, firstname, lastname, address, address2, contact_info, status, created_on) VALUES (:email, :password, :firstname, :lastname, :address, :address2, :contact_info, :status, :created_on)");
            $stmt->execute([
                'email' => $_SESSION['temp_user_data']['email'],
                'password' => $password,
                'firstname' => $_SESSION['temp_user_data']['firstname'],
                'lastname' => $_SESSION['temp_user_data']['lastname'],
                'address' => $_SESSION['temp_user_data']['address'],
                'address2' => $_SESSION['temp_user_data']['address2'],
                'contact_info' => $_SESSION['temp_user_data']['contact_info'],
                'status' => 1,
                'created_on' => $now,
            ]);

            unset($_SESSION['temp_user_data']);
            $_SESSION['success'] = 'Account created successfully!';
            header('location: login');
            exit();
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Error: ' . $e->getMessage();
            header('location: signup');
            exit();
        }

        $pdo->close();
    } else {
        $_SESSION['error'] = 'Incorrect verification code.';
        header('location: verify_email');
        exit();
    }
}

if(isset($_POST['resend'])){
    $verification_code = sprintf("%06d", mt_rand(1, 999999));
    $email = $_SESSION['temp_user_data']['email'];
    $firstname = $_SESSION['temp_user_data']['firstname'];
    $lastname = $_SESSION['temp_user_data']['lastname'];

    if(sendVerificationEmail($email, $firstname, $lastname, $verification_code)){
        $_SESSION['temp_user_data']['verification_code'] = $verification_code;
        $_SESSION['temp_user_data']['code_time'] = time();
        $_SESSION['success'] = 'New verification code sent to your email.';
    } else {
        $_SESSION['error'] = 'Failed to send new verification code. Please try again.';
    }
    header('location: verify_email');
    exit();
}

// Include header after processing
include 'includes/header.php';

// Calculate remaining time
$remaining_time = max(0, 300 - (time() - $_SESSION['temp_user_data']['code_time']));
$remaining_minutes = floor($remaining_time / 60);
$remaining_seconds = $remaining_time % 60;
$is_expired = $remaining_time == 0;
?>
<script>
    var timeLeft = <?php echo $remaining_time; ?>;
    var timerId = setInterval(countdown, 1000);
    var verifyButton = document.getElementById("verifyButton");
    var resendButton = document.getElementById("resendButton");

    function countdown() {
        if (timeLeft <= 0) {
            clearTimeout(timerId);
            document.getElementById("countdown").innerHTML = "Expired";
            verifyButton.disabled = true;
            resendButton.style.display = "inline-block";
        } else {
            var minutes = Math.floor(timeLeft / 60);
            var seconds = timeLeft % 60;
            document.getElementById("countdown").innerHTML = minutes + "m " + seconds + "s";
            timeLeft--;
        }
    }
</script>
<body>
    <br><br><br><br>
<div class="container2">
    <a href="login" style="color: rgb(0, 51, 102); "><i class="fa fa-arrow-left" style="color: rgb(0, 51, 102);"></i></a></p>
    <h2 class="login-box-msg" style="font-size: 20px; color:  rgb(0, 51, 102);">Enter Email Verification Code</h2>
        <b><p>Time remaining: <span id="countdown"><?php echo $is_expired ? "Expired" : $remaining_minutes . "m " . $remaining_seconds . "s"; ?></span></p></b>
        <?php
    if(isset($_SESSION['error'])){
        echo "
            <div class='error'>
                ".$_SESSION['error']."
            </div>
        ";
        unset($_SESSION['error']);
    }
    ?>
    <form action="verify_email" method="POST">
        <label for="verification_code">Enter Verification Code:</label>
        <input type="text" name="verification_code" required>
        <button type="submit" name="verify" id="verifyButton">Verify</button>
    </form>
    <form action="verify_email" method="POST">
        <button type="submit" name="resend" id="resendButton" class="resend-button" <?php echo !$is_expired ? 'style="display:none;"' : ''; ?>>Resend Code</button>
    </form>
</div>
<script src="js/sweetalert.min.js"></script>
<style>
.resend-button {
            background-color: #4CAF50;
            margin-top: 10px;
        }


  body{
    background: rgb(0, 51, 102);
    background-size: cover;
    background-repeat: no-repeat;
  }
  .container2 { 
    width: 500px;
    height: 300px;
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
