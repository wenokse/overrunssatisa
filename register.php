<?php
include 'includes/session.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function containsSpecialCharacters($str) {
    return preg_match('/[<>:\/\$\;\,\?\!]/', $str);
}

if (isset($_POST['signup'])) {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $address = $_POST['address'];
    $address2 = $_POST['address2'];
    $contact_info = $_POST['contact_info'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $repassword = $_POST['repassword'];

    if (!isset($_SESSION['captcha'])) {
        require('recaptcha/src/autoload.php');
    
        // Your secret key for verification
        $secretKey = '6Lf-VoIqAAAAAIXG5tzEBzI814o8JbZVs61dfiVk';
    
        // Retrieve the response from the form
        $recaptchaResponse = $_POST['g-recaptcha-response'];
    
        // Verify the response with Google's reCAPTCHA API
        $recaptcha = new \ReCaptcha\ReCaptcha($secretKey, new \ReCaptcha\RequestMethod\SocketPost());
        $resp = $recaptcha->verify($recaptchaResponse, $_SERVER['REMOTE_ADDR']);
    
        // Check if the response is valid
        if (!$resp->isSuccess() || $resp->getScore() < 0.5) {
            // Failed reCAPTCHA or low confidence score
            $_SESSION['error'] = 'Failed reCAPTCHA verification. Please try again.';
            header('location: signup.php');
            exit();
        } else {
            // Set session variable for captcha validation
            $_SESSION['captcha'] = time() + (10 * 60); // 10 minutes
        }
    }
    
    // Input validation
    if (containsSpecialCharacters($firstname) || containsSpecialCharacters($lastname) ||
        containsSpecialCharacters($email) || containsSpecialCharacters($password)) {
        $_SESSION['error'] = 'Special characters like <>:/$;,?! are not allowed.';
        header('location: signup');
        exit();
    }

    if ($password != $repassword) {
        $_SESSION['error'] = 'Passwords did not match.';
        header('location: signup');
        exit();
    }

    if (strpos($email, '@gmail.com') === false) {
        $_SESSION['error'] = 'Email must be a @gmail.com address.';
        header('location: signup');
        exit();
    }

    // Validate phone number
    if (!preg_match("/^09\d{9}$/", $contact_info)) {
        $_SESSION['error'] = "Invalid phone number format. Please enter a valid 11-digit number starting with 09.";
        header('location: signup');
        exit();
    }

    $conn = $pdo->open();

    // Check if email exists
    $stmt = $conn->prepare("SELECT COUNT(*) AS numrows FROM users WHERE email=:email");
    $stmt->execute(['email' => $email]);
    $row = $stmt->fetch();

    if ($row['numrows'] > 0) {
        $_SESSION['error'] = 'Email already taken.';
        header('location: signup');
        exit();
    }

    // Generate verification codes
    $email_code = sprintf("%06d", mt_rand(1, 999999));
    $sms_code = sprintf("%06d", mt_rand(1, 999999));
    $code_expiry = time() + 600; // 10 minutes expiry

    try {
        // Send Email
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'overrunssatisa@gmail.com';
        $mail->Password = 'ahuf cbzv bpph caje';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        $mail->setFrom('overrunssatisa@gmail.com', 'Overruns Sa Tisa Online Shop');
        $mail->addAddress($email, "$firstname $lastname");

        $mail->isHTML(true);
        $mail->Subject = 'Email Verification Code';
        $mail->Body = "Your verification code is: <b>$email_code</b>";

        $mail->send();

        // Send SMS
        $international_format = '+63' . substr($contact_info, 1);
        
                    // Infobip API Configuration
            $base_url = 'https://69y84d.api.infobip.com'; // Verify your base URL
            $api_key = 'f8e95ad451e731b7d04c6c087427a1a5-bbc9cd9a-f53a-4c3c-be91-9ce46618dd72';

            $payload = [
                'messages' => [
                    [
                        'from' => 'OverrunsSaTisa',
                        'destinations' => [
                            ['to' => $international_format]
                        ],
                        'text' => "Your SMS verification code is: $sms_code. Valid for 10 minutes.",
                        'flash' => false,
                        'validityPeriod' => 600
                    ]
                ]
            ];

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $base_url . '/sms/2/text/advanced',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Authorization: App ' . $api_key
                ],
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_TIMEOUT => 60, // Increased timeout
                CURLOPT_CONNECTTIMEOUT => 30 // Connection timeout
            ]);

            $response = curl_exec($curl);
            $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            if ($response === false) {
                $curl_error = curl_error($curl);
                error_log("Curl Error: " . $curl_error);
                throw new Exception("SMS sending failed: " . $curl_error);
            }

            $response_data = json_decode($response, true);

            // Additional logging for debugging
            error_log("HTTP Code: $http_code");
            error_log("Infobip Response: " . json_encode($response_data, JSON_PRETTY_PRINT));

            if ($http_code != 200 || !isset($response_data['messages'][0]['status']['groupId'])) {
                $status_message = $response_data['messages'][0]['status']['description'] ?? 'Unknown error';
                throw new Exception("SMS API error: $status_message");
            }

            curl_close($curl);


            $_SESSION['temp_user_data'] = [
                'firstname' => $firstname,
                'lastname' => $lastname,
                'address' => $address,
                'address2' => $address2,
                'contact_info' => $contact_info,
                'email' => $email,
                'password' => $password,
                'email_code' => $email_code,
                'sms_code' => $sms_code,
                'code_time' => $code_expiry,
            ];
    
            $_SESSION['success'] = 'Verification codes sent to your email and phone. Please check both.';
            header('location: verify_email');
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = 'Verification process failed: ' . $e->getMessage();
            header('location: signup');
            exit();
        }
    
        $pdo->close();
    } else {
        $_SESSION['error'] = 'Fill up signup form first.';
        header('location: signup');
        exit();
    }
?>