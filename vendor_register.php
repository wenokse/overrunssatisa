<?php
include 'includes/session.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function containsSpecialCharacters($str) {
    return preg_match('/[<>:\/\$\;\,\?\!]/', $str);
}

if(isset($_POST['signup'])){
    $store = $_POST['store'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $address = $_POST['address'];
    $address2 = $_POST['address2'];
    $contact_info = $_POST['contact_info'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $repassword = $_POST['repassword'];
    $tin_number = $_POST['tin_number'];

    if (isset($_POST['recaptcha_response'])) {
        $recaptchaResponse = $_POST['recaptcha_response'];
        $secretKey = '6Lf-VoIqAAAAALGiTwK15qjAKTRD6Kv8al322Apf';
        
        $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$recaptchaResponse}");
        $responseKeys = json_decode($response, true);
        
        if (!$responseKeys['success'] || $responseKeys['score'] < 0.5) {
            $_SESSION['error'] = 'reCAPTCHA verification failed. Please try again.';
            header('Location: vendor_signup');
            exit();
        }
    }

    // Validate for special characters
    if (containsSpecialCharacters($firstname) || containsSpecialCharacters($lastname) || 
        containsSpecialCharacters($email) || containsSpecialCharacters($password) || 
        containsSpecialCharacters($store)) {
        $_SESSION['error'] = 'Special characters like <>:/$;,?! are not allowed.';
        header('location: vendor_signup');
        exit();
    }

    // Validate Gmail address
    if (strpos($email, '@gmail.com') === false) {
        $_SESSION['error'] = 'Email must be a @gmail.com address';
        header('location: vendor_signup');
        exit();
    }

    // Password match validation
    if($password != $repassword){
        $_SESSION['error'] = 'Passwords did not match';
        header('location: vendor_signup');
        exit();
    }

    try {
        // Validate required files
        $required_files = ['photo', 'valid_id', 'bir_doc', 'dti_doc', 'mayor_permit'];
        foreach ($required_files as $file) {
            if(!isset($_FILES[$file]) || $_FILES[$file]['error'] === UPLOAD_ERR_NO_FILE) {
                throw new Exception(ucfirst(str_replace('_', ' ', $file)) . ' is required');
            }
        }

        $conn = $pdo->open();

        // Check if email exists
        $stmt = $conn->prepare("SELECT COUNT(*) AS numrows FROM users WHERE email=:email");
        $stmt->execute(['email'=>$email]);
        $row = $stmt->fetch();

        if($row['numrows'] > 0){
            throw new Exception('Email already taken');
        }

        // Generate verification code
        $verification_code = sprintf("%06d", mt_rand(1, 999999));

        // Send verification email
        $mail = new PHPMailer(true);

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
        
        //Recipients
        $mail->setFrom('overrunssatisa@gmail.com', 'Overruns Sa Tisa Online Shop');
        $mail->addAddress($email, $firstname . ' ' . $lastname);

        //Content
        $mail->isHTML(true);
        $mail->Subject = 'Vendor Registration - Email Verification Code';
        $mail->Body    = "<h2>Thank you for registering as a vendor with our shop.<br>Your verification code is: <b>$verification_code</b></h2>";

        $mail->send();

        // Store all data in session including file data
        $_SESSION['temp_vendor_data'] = [
            'store' => $store,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'address' => $address,
            'address2' => $address2,
            'contact_info' => $contact_info,
            'email' => $email,
            'password' => $password,
            'tin_number' => $tin_number,
            'verification_code' => $verification_code,
            'code_time' => time()
        ];

        // Store file data separately to avoid potential session size issues
        $_SESSION['temp_vendor_files'] = [];
        foreach ($required_files as $file) {
            $_SESSION['temp_vendor_files'][$file] = [
                'name' => $_FILES[$file]['name'],
                'type' => $_FILES[$file]['type'],
                'tmp_name' => $_FILES[$file]['tmp_name'],
                'error' => $_FILES[$file]['error'],
                'size' => $_FILES[$file]['size']
            ];
            
            // Move uploaded file to temporary location
            $temp_dir = sys_get_temp_dir() . '/vendor_uploads/';
            if (!is_dir($temp_dir)) {
                mkdir($temp_dir, 0777, true);
            }
            
            $temp_file = $temp_dir . uniqid() . '_' . basename($_FILES[$file]['name']);
            if (!move_uploaded_file($_FILES[$file]['tmp_name'], $temp_file)) {
                throw new Exception("Error moving uploaded file: " . $_FILES[$file]['name']);
            }
            $_SESSION['temp_vendor_files'][$file]['temp_path'] = $temp_file;
        }

        $_SESSION['success'] = 'Verification code sent to your email. Please check your inbox.';
        header('location: verify_vendor_email');
        exit();

    } catch (Exception $e) {
        error_log("Error in vendor registration: " . $e->getMessage());
        $_SESSION['error'] = $e->getMessage();
        header('location: vendor_signup');
        exit();
    } finally {
        if(isset($conn)) {
            $pdo->close();
        }
    }
} else {
    $_SESSION['error'] = 'Fill up signup form first';
    header('location: vendor_signup');
    exit();
}
?>