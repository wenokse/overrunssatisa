<?php
include 'includes/session.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Initial selection page
if (!isset($_POST['reset_type']) && !isset($_POST['email']) && !isset($_POST['contact_info'])) {
    include 'includes/header.php';
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <script src="js/sweetalert.min.js"></script>
    </head>
    <body>
    <br><br><br><br>
    <div class="container2">
        <a href="login" style="color: rgb(0, 51, 102);"><i class="fa fa-arrow-left"></i></a>
        <center><h2>Forgot Password</h2></center><br>
        
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
        
        <!-- Selection Options -->
        <div class="reset-methods">
            <button onclick="showForm('email_otp')" class="reset-option">
                <i class="fa fa-envelope"></i>
                <div class="option-text">
                    <span class="option-title">Reset via Email OTP</span>
                    <span class="option-desc">Receive a code via email</span>
                </div>
            </button>

            <button onclick="showForm('email_link')" class="reset-option">
                <i class="fa fa-link"></i>
                <div class="option-text">
                    <span class="option-title">Reset via Email Link</span>
                    <span class="option-desc">Receive a reset link via email</span>
                </div>
            </button>

            <button onclick="showForm('sms')" class="reset-option">
                <i class="fa fa-mobile"></i>
                <div class="option-text">
                    <span class="option-title">Reset via SMS</span>
                    <span class="option-desc">Receive a code via SMS</span>
                </div>
            </button>
        </div>

        <!-- Email OTP Form -->
        <form id="emailOtpForm" action="password_forgot" method="POST" style="display: none;" class="reset-form">
            <input type="hidden" name="reset_type" value="email_otp">
            <input type="email" name="email" placeholder="Enter your email" required>
            <button type="submit" class="submit-btn">Send OTP</button>
            <button type="button" onclick="showMethods()" class="back-btn">Back</button>
        </form>

        <!-- Email Link Form -->
        <form id="emailLinkForm" action="password_forgot" method="POST" style="display: none;" class="reset-form">
            <input type="hidden" name="reset_type" value="email_link">
            <input type="email" name="email" placeholder="Enter your email" required>
            <button type="submit" class="submit-btn">Send Reset Link</button>
            <button type="button" onclick="showMethods()" class="back-btn">Back</button>
        </form>

        <!-- SMS Form -->
        <form id="smsForm" action="password_forgot" method="POST" style="display: none;" class="reset-form">
            <input type="hidden" name="reset_type" value="sms">
            <input type="tel" name="contact_info" 
                   placeholder="Enter your number (e.g., 09123456789)" 
                   pattern="09[0-9]{9}"
                   maxlength="11"
                   required>
            <button type="submit" class="submit-btn">Send OTP via SMS</button>
            <button type="button" onclick="showMethods()" class="back-btn">Back</button>
        </form>
    </div>

    <style>
    body {
        background: rgb(0, 51, 102);
        background-size: cover;
        background-repeat: no-repeat;
    }
    .container2 { 
        width: 500px;
        height: auto;
        margin: 0 auto 50px;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 20px;
        background: linear-gradient(135deg, #6e8efb, #a777e3);
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
    }
    .reset-methods {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    .reset-option {
        display: flex;
        align-items: center;
        width: 100%;
        padding: 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        background-color: white;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .reset-option:hover {
        background-color: #f0f0f0;
        border-color: #512da8;
    }
    .reset-option i {
        font-size: 24px;
        margin-right: 15px;
        color: #512da8;
    }
    .option-text {
        text-align: left;
    }
    .option-title {
        display: block;
        font-weight: bold;
        color: #333;
    }
    .option-desc {
        display: block;
        font-size: 12px;
        color: #666;
    }
    .reset-form {
        margin-top: 20px;
    }
    .reset-form input {
        width: 100%;
        padding: 12px;
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
    }
    .submit-btn, .back-btn {
        width: 100%;
        padding: 12px;
        border: none;
        border-radius: 20px;
        font-weight: 600;
        cursor: pointer;
        margin-bottom: 10px;
    }
    .submit-btn {
        background-color: #512da8;
        color: white;
    }
    .back-btn {
        background-color: #f0f0f0;
        color: #333;
    }
    .submit-btn:hover {
        background-color: #4527a0;
    }
    .back-btn:hover {
        background-color: #e0e0e0;
    }
    </style>

    <script>
    function showForm(type) {
        document.querySelector('.reset-methods').style.display = 'none';
        document.getElementById('emailOtpForm').style.display = 'none';
        document.getElementById('emailLinkForm').style.display = 'none';
        document.getElementById('smsForm').style.display = 'none';
        
        switch(type) {
            case 'email_otp':
                document.getElementById('emailOtpForm').style.display = 'block';
                break;
            case 'email_link':
                document.getElementById('emailLinkForm').style.display = 'block';
                break;
            case 'sms':
                document.getElementById('smsForm').style.display = 'block';
                break;
        }
    }

    function showMethods() {
        document.querySelector('.reset-methods').style.display = 'flex';
        document.getElementById('emailOtpForm').style.display = 'none';
        document.getElementById('emailLinkForm').style.display = 'none';
        document.getElementById('smsForm').style.display = 'none';
    }

    // Phone number validation
    document.querySelector('input[name="contact_info"]').addEventListener('input', function(e) {
        let number = this.value.replace(/\D/g, '');
        if (number.length >= 2 && number.substring(0, 2) !== '09') {
            number = '09' + number.substring(2);
        }
        number = number.substring(0, 11);
        this.value = number;
    });
    </script>
    </body>
    </html>

    <?php
    exit();
}

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reset_type = $_POST['reset_type'] ?? '';
    
    switch($reset_type) {
        case 'email_otp':
            handleEmailOTP();
            break;
        case 'email_link':
            handleEmailLink();
            break;
        case 'sms':
            handleSMS();
            break;
        default:
            $_SESSION['error'] = "Invalid reset method.";
            header('location: password_forgot');
            exit();
    }
}

function handleEmailOTP() {
    global $pdo;
    
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email address.";
        header('location: password_forgot');
        exit();
    }

    $otp = sprintf("%06d", mt_rand(0, 999999));
    $expiry = time() + 600; // 10 minutes

    try {
        $conn = $pdo->open();
        // First check if email exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        
        if ($stmt->rowCount() == 0) {
            $_SESSION['error'] = 'Email not found in our records.';
            header('location: password_forgot');
            exit();
        }
        
        // Update the reset code
        $stmt = $conn->prepare("UPDATE users SET reset_code = :reset_code, reset_code_expiry = :expiry WHERE email = :email");
        $stmt->execute(['reset_code' => $otp, 'expiry' => $expiry, 'email' => $email]);

        if ($stmt->rowCount() > 0) {
            sendEmail($email, 'Password Reset OTP', "Your OTP for password reset is: <strong>$otp</strong>. It will expire in 10 minutes.");
            $_SESSION['reset_email'] = $email;
            $_SESSION['reset_time'] = $expiry;
            header('location: reset_verify');
            exit();
        }
    } catch(PDOException $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
        header('location: password_forgot');
    }
    $pdo->close();
    exit();
}

function handleEmailLink() {
    global $pdo;

    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email address.";
        header('location: password_forgot');
        exit();
    }

    // Generate a secure reset code
    $reset_code = bin2hex(random_bytes(15)); // 30-character secure random string
    $reset_code_hash = password_hash($reset_code, PASSWORD_DEFAULT); // Hash the code
    $expiry = time() + 3600; // 1 hour

    try {
        $conn = $pdo->open();

        // Update the database with the hashed reset code
        $stmt = $conn->prepare("UPDATE users SET reset_code = :reset_code, reset_code_expiry = :expiry WHERE email = :email");
        $stmt->execute(['reset_code' => $reset_code_hash, 'expiry' => $expiry, 'email' => $email]);

        if ($stmt->rowCount() > 0) {
            $reset_link = "https://overrunssatisa.com/password_reset?code=" . urlencode($reset_code) . "&email=" . urlencode($email);
            sendEmail($email, 'Password Reset Link', "Click the following link to reset your password: <a href='$reset_link'>Reset Password</a><br>This link will expire in 1 hour.");
            $_SESSION['success'] = 'Password reset link has been sent to your email.';
        } else {
            $_SESSION['error'] = 'Email not found in our records.';
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
    }

    $pdo->close();
    header('location: password_forgot');
    exit();
}

  
function handleSMS() {
    global $pdo;
    
    $contact_info = filter_var($_POST["contact_info"], FILTER_SANITIZE_STRING);
    if (!preg_match("/^09\d{9}$/", $contact_info)) {
        $_SESSION['error'] = "Invalid phone number format. Please enter a valid 11-digit number starting with 09.";
        header('location: password_forgot');
        exit();
    }

    $international_format = '+63' . substr($contact_info, 1);
    $otp = sprintf("%06d", mt_rand(0, 999999));
    $expiry = time() + 600; // 10 minutes

    try {
        $conn = $pdo->open();
        $stmt = $conn->prepare("SELECT id FROM users WHERE contact_info = :contact_info");
        $stmt->execute(['contact_info' => $contact_info]);

        if ($stmt->rowCount() == 0) {
            $_SESSION['error'] = 'Phone number not found in our records.';
            header('location: password_forgot');
            exit();
        }

        $stmt = $conn->prepare("UPDATE users SET reset_code = :reset_code, reset_code_expiry = :expiry WHERE contact_info = :contact_info");
        $stmt->execute([
            'reset_code' => $otp,
            'expiry' => $expiry,
            'contact_info' => $contact_info
        ]);

        // Infobip API Configuration
        $base_url = 'https://69y84d.api.infobip.com';
        $api_key = 'f8e95ad451e731b7d04c6c087427a1a5-bbc9cd9a-f53a-4c3c-be91-9ce46618dd72';

        $payload = [
            'messages' => [
                [
                    'from' => 'OverrunsSaTisa',
                    'destinations' => [
                        ['to' => $international_format]
                    ],
                    'text' => "Your OTP for password reset is: $otp. Valid for 10 minutes.",
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
            CURLOPT_TIMEOUT => 30
        ]);

        $response = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($response === false) {
            error_log("SMS sending failed. cURL Error: " . curl_error($curl));
            $_SESSION['error'] = "Failed to send SMS. Please try again later.";
          } else {
            $response_data = json_decode($response, true);
            
            if ($http_code === 200 && isset($response_data['messages'][0]['status']['groupId']) && $response_data['messages'][0]['status']['groupId'] === 1) {
                $_SESSION['reset_phone'] = $contact_info;
                $_SESSION['reset_time'] = $expiry;
                header('location: reset_verify');
                exit();
            } else {
                error_log("SMS API Error: " . $response);
                $_SESSION['error'] = "Failed to send SMS. Please try again or use email reset.";
            }
        }
        
        curl_close($curl);
        
    } catch(PDOException $e) {
        error_log("Database Error in handleSMS: " . $e->getMessage());
        $_SESSION['error'] = "System error occurred. Please try again later.";
    }
    
    $pdo->close();
    header('location: password_forgot');
    exit();
}

function sendEmail($to, $subject, $body) {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
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

        // Recipients
        $mail->setFrom('overrunssatisa@gmail.com', 'Overruns Sa Tisa Online Shop');
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
       
    } catch (Exception $e) {
        error_log("Email sending failed: " . $e->getMessage());
        $_SESSION['error'] = "Failed to send email. Please try again later.";
    }
}

// Add security headers
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline';");
?>