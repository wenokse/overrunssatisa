<?php
include 'includes/session.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function containsSpecialCharacters($str) {
    // Regular expression to match the special characters <>:/$;,?!
    return preg_match('/[<>:\/\$\;\,\?\!]/', $str);
}

if(isset($_POST['signup'])){
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $address = $_POST['address'];
    $address2 = $_POST['address2'];
    $contact_info = $_POST['contact_info'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $repassword = $_POST['repassword'];

    if(!isset($_SESSION['captcha'])){
        require('recaptcha/src/autoload.php');
        $recaptcha = new \ReCaptcha\ReCaptcha('6LdGIWQfAAAAAMzd7G5PAdIeEhqqZHO-dgBrZeMo', new \ReCaptcha\RequestMethod\SocketPost());
        $resp = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);

        if (!$resp->isSuccess()){
              $_SESSION['error'] = 'Please answer recaptcha correctly';
              header('location: signup.php');
              exit();	
          }	
          else{
              $_SESSION['captcha'] = time() + (10*60);
          }

    }

    if (containsSpecialCharacters($firstname) || containsSpecialCharacters($lastname) || containsSpecialCharacters($email) || containsSpecialCharacters($password)) {
        $_SESSION['error'] = 'Special characters like <>:/$;,?! are not allowed.';
        header('location: signup.php');
        exit();
    }

    if($password != $repassword){
        $_SESSION['error'] = 'Passwords did not match';
        header('location: signup.php');
        exit();
    }
    if (strpos($email, '@gmail.com') === false) {
        $_SESSION['error'] = 'Email must be a @gmail.com address';
        header('location: signup.php');
        exit();
    }

    $conn = $pdo->open();

    $stmt = $conn->prepare("SELECT COUNT(*) AS numrows FROM users WHERE email=:email");
    $stmt->execute(['email'=>$email]);
    $row = $stmt->fetch();

    if($row['numrows'] > 0){
        $_SESSION['error'] = 'Email already taken';
        header('location: signup.php');
        exit();
    }

    // Generate verification code
    $verification_code = sprintf("%06d", mt_rand(1, 999999));

    try {
        $mail = new PHPMailer(true);

        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;
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
        $mail->Timeout = 300; // in seconds
        $mail->SMTPKeepAlive = true;


        //Recipients
        $mail->setFrom('overrunssatisa@gmail.com', 'Overruns Sa Tisa Online Shop');
        $mail->addAddress($email, $firstname . ' ' . $lastname);

        //Content
        $mail->isHTML(true);
        $mail->Subject = 'Email Verification Code';
        $mail->Body    = "Thank you for registering to our shop; <br>  Your verification code is: <b>$verification_code</b>";

        $mail->send();
        $_SESSION['success'] = 'Verification code sent to your email. Please check your inbox.';

        // Store user data in session
        $_SESSION['temp_user_data'] = [
            'firstname' => $firstname,
            'lastname' => $lastname,
            'address' => $address,
            'address2' => $address2,
            'contact_info' => $contact_info,
            'email' => $email,
            'password' => $password,
            'verification_code' => $verification_code,
			'code_time' => time() 
        ];

        header('location: verify_email.php');
        exit();
    } catch (Exception $e) {
        error_log("Failed to send email. Error: " . $e->getMessage());
        $_SESSION['error'] = "Message could not be sent. Error: " . $e->getMessage();
        header('location: signup.php');
        exit();
    }

    $pdo->close();
} else {
    $_SESSION['error'] = 'Fill up signup form first';
    header('location: signup.php');
    exit();
}
?>