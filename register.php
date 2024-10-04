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

function isValidPassword($password) {
    // Check if password has at least one uppercase letter, one lowercase letter, one number, and no special characters
    return preg_match('/[A-Z]/', $password) && preg_match('/[a-z]/', $password) && preg_match('/[0-9]/', $password) && !containsSpecialCharacters($password);
}

if(isset($_POST['signup'])){
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $address = trim($_POST['address']);
    $address2 = trim($_POST['address2']);
    $contact_info = trim($_POST['contact_info']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $repassword = $_POST['repassword'];

    if(!isset($_SESSION['captcha'])){
        require('recaptcha/src/autoload.php');
        $recaptcha = new \ReCaptcha\ReCaptcha('6LfmdVQqAAAAAELMHS60poazcKSqrkR8DU2Me7OY', new \ReCaptcha\RequestMethod\SocketPost());
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

    // Check if any field is empty or consists of only spaces
    if (empty($firstname) || empty($lastname) || empty($email) || empty($password) || empty($address) || empty($contact_info)) {
        $_SESSION['error'] = 'Please fill out all required fields properly.';
        header('location: signup.php');
        exit();
    }

    // Check for special characters in firstname, lastname, email, or password
    if (containsSpecialCharacters($firstname) || containsSpecialCharacters($lastname) || containsSpecialCharacters($email)) {
        $_SESSION['error'] = 'Special characters are not allowed.';
        header('location: signup.php');
        exit();
    }

    // Password validation
    if (!isValidPassword($password)) {
        $_SESSION['error'] = 'Password must contain at least one uppercase letter, one lowercase letter, one number, and no special characters.';
        header('location: signup.php');
        exit();
    }

    // Check if passwords match
    if ($password != $repassword) {
        $_SESSION['error'] = 'Passwords did not match';
        header('location: signup.php');
        exit();
    }

    // Check if the email is a @gmail.com address
    if (strpos($email, '@gmail.com') === false) {
        $_SESSION['error'] = 'Email must be a @gmail.com address';
        header('location: signup.php');
        exit();
    }

    // Database operations
    $conn = $pdo->open();

    // Check if the email already exists
    $stmt = $conn->prepare("SELECT COUNT(*) AS numrows FROM users WHERE email=:email");
    $stmt->execute(['email' => $email]);
    $row = $stmt->fetch();

    if($row['numrows'] > 0) {
        $_SESSION['error'] = 'Email already taken';
        header('location: signup.php');
        exit();
    }

    // Generate verification code
    $verification_code = sprintf("%06d", mt_rand(1, 999999));

    // Email sending using PHPMailer
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

        // Recipients
        $mail->setFrom('overrunssatisa@gmail.com', 'Overruns Sa Tisa Online Shop');
        $mail->addAddress($email, $firstname . ' ' . $lastname);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Email Verification Code';
        $mail->Body    = "Thank you for registering to our shop; <br> Your verification code is: <b>$verification_code</b>";

        $mail->send();
        $_SESSION['success'] = 'Verification code sent to your email. Please check your inbox.';

        // Store user data temporarily in session
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
