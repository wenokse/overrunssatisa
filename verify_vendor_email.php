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

        //Recipients
        $mail->setFrom('overrunssatisa@gmail.com', 'Overruns Sa Tisa Online Shop');
        $mail->addAddress($email, $firstname . ' ' . $lastname);

        //Content
        $mail->isHTML(true);
        $mail->Subject = 'Vendor Registration - Email Verification Code';
        $mail->Body    = "Thank you for registering as a vendor with our shop.<br>Your verification code is: <b>$verification_code</b>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Failed to send email. Error: " . $e->getMessage());
        return false;
    }
}

// Function to handle file uploads
function handleFileUpload($file_data, $upload_path, $allowed_extensions, $max_file_size) {
    if (!isset($file_data['temp_path']) || !file_exists($file_data['temp_path'])) {
        throw new Exception('Temporary file not found');
    }

    if($file_data['size'] > $max_file_size) {
        throw new Exception('File size exceeds 5MB limit');
    }

    $file_ext = strtolower(pathinfo($file_data['name'], PATHINFO_EXTENSION));
    if(!in_array($file_ext, $allowed_extensions)) {
        throw new Exception('Invalid file type');
    }

    $filename = time() . '_' . uniqid() . '.' . $file_ext;
    $target_path = $upload_path . $filename;

    if(!is_dir($upload_path)) {
        if (!mkdir($upload_path, 0777, true)) {
            throw new Exception('Failed to create upload directory');
        }
    }

    if(!copy($file_data['temp_path'], $target_path)) {
        throw new Exception('Error uploading file');
    }

    // Clean up temporary file
    @unlink($file_data['temp_path']);

    return $filename;
}

// Process form submission
if(isset($_POST['verify'])){
    if(!isset($_SESSION['temp_vendor_data']) || !isset($_SESSION['temp_vendor_files'])) {
        $_SESSION['error'] = 'Session expired. Please register again.';
        header('location: vendor_signup');
        exit();
    }

    $user_code = $_POST['verification_code'];
    $stored_code = $_SESSION['temp_vendor_data']['verification_code'];
    $code_time = $_SESSION['temp_vendor_data']['code_time'];

    // Check if the code has expired (5 minutes = 300 seconds)
    if(time() - $code_time > 300){
        $_SESSION['error'] = 'Verification code has expired. Please request a new one.';
        header('location: verify_vendor_email');
        exit();
    }

    if($user_code == $stored_code){
        // Verification successful, create vendor account
        $conn = $pdo->open();
        $now = date('Y-m-d');
        $password = password_hash($_SESSION['temp_vendor_data']['password'], PASSWORD_DEFAULT);

        try {
            // Handle file uploads
            $upload_path = 'images/';
            $max_file_size = 5 * 1024 * 1024;
            $image_extensions = ['jpg', 'jpeg', 'png'];
            $document_extensions = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];

            // Process uploads using temporary files
            $files = $_SESSION['temp_vendor_files'];
            
            $photo = handleFileUpload($files['photo'], $upload_path, $image_extensions, $max_file_size);
            $valid_id = handleFileUpload($files['valid_id'], $upload_path, $image_extensions, $max_file_size);
            $bir_doc = handleFileUpload($files['bir_doc'], $upload_path, $document_extensions, $max_file_size);
            $dti_doc = handleFileUpload($files['dti_doc'], $upload_path, $document_extensions, $max_file_size);
            $mayor_permit = handleFileUpload($files['mayor_permit'], $upload_path, $document_extensions, $max_file_size);

            // Insert vendor data
            $stmt = $conn->prepare("INSERT INTO users (email, password, firstname, lastname, store, address, address2, 
                contact_info, photo, type, status, bir_doc, dti_doc, mayor_permit, valid_id, 
                tin_number, created_on) VALUES (:email, :password, :firstname, :lastname, :store, :address, :address2, 
                :contact_info, :photo, :type, :status, :bir_doc, :dti_doc, :mayor_permit, :valid_id, 
                :tin_number, :created_on)");
                
            $stmt->execute([
                'email' => $_SESSION['temp_vendor_data']['email'],
                'password' => $password,
                'firstname' => $_SESSION['temp_vendor_data']['firstname'],
                'lastname' => $_SESSION['temp_vendor_data']['lastname'],
                'store' => $_SESSION['temp_vendor_data']['store'],
                'address' => $_SESSION['temp_vendor_data']['address'],
                'address2' => $_SESSION['temp_vendor_data']['address2'],
                'contact_info' => $_SESSION['temp_vendor_data']['contact_info'],
                'photo' => $photo,
                'type' => 2, // vendor type
                'status' => 3, // pending approval
                'bir_doc' => $bir_doc,
                'dti_doc' => $dti_doc,
                'mayor_permit' => $mayor_permit,
                'valid_id' => $valid_id,
                'tin_number' => $_SESSION['temp_vendor_data']['tin_number'],
                'created_on' => $now
            ]);

            // Clean up
            unset($_SESSION['temp_vendor_data']);
            unset($_SESSION['temp_vendor_files']);
            
            $_SESSION['success'] = 'Account created successfully, waiting for admin approval.';
            header('location: login');
            exit();
        }
        catch (Exception $e){
            $_SESSION['error'] = $e->getMessage();
            header('location: vendor_signup');
            exit();
        }

        $pdo->close();
    } else {
        $_SESSION['error'] = 'Incorrect verification code';
        header('location: verify_vendor_email');
        exit();
    }
}


if(isset($_POST['resend'])){
    $verification_code = sprintf("%06d", mt_rand(1, 999999));
    $email = $_SESSION['temp_vendor_data']['email'];
    $firstname = $_SESSION['temp_vendor_data']['firstname'];
    $lastname = $_SESSION['temp_vendor_data']['lastname'];

    if(sendVerificationEmail($email, $firstname, $lastname, $verification_code)){
        $_SESSION['temp_vendor_data']['verification_code'] = $verification_code;
        $_SESSION['temp_vendor_data']['code_time'] = time();
        $_SESSION['success'] = 'New verification code sent to your email.';
    } else {
        $_SESSION['error'] = 'Failed to send new verification code. Please try again.';
    }
    header('location: verify_vendor_email');
    exit();
}

// Include header after processing
include 'includes/header.php';

// Calculate remaining time
$remaining_time = max(0, 300 - (time() - $_SESSION['temp_vendor_data']['code_time']));
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
        <a href="login" style="color: rgb(0, 51, 102);"><i class="fa fa-arrow-left" style="color: rgb(0, 51, 102);"></i></a>
        <h2 class="login-box-msg" style="font-size: 20px; color: rgb(0, 51, 102);">Vendor Email Verification</h2>
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
        if(isset($_SESSION['success'])){
            echo "
                <div class='success'>
                    ".$_SESSION['success']."
                </div>
            ";
            unset($_SESSION['success']);
        }
        ?>
        <form action="verify_vendor_email" method="POST">
            <label for="verification_code">Enter Verification Code:</label>
            <input type="text" name="verification_code" required pattern="[0-9]{6}" 
                   maxlength="6" 
                   placeholder="Enter 6-digit OTP" 
                   title="Please enter 6-digit OTP"
                   autocomplete="one-time-code">
            <button type="submit" name="verify" id="verifyButton">Verify</button>
        </form>
        <form action="verify_vendor_email" method="POST">
            <button type="submit" name="resend" id="resendButton" class="resend-button" <?php echo !$is_expired ? 'style="display:none;"' : ''; ?>>Resend Code</button>
        </form>
    </div>
    <script src="js/sweetalert.min.js"></script>
    <style>
/* General Body Styling */
body {
    background: rgb(0, 51, 102);
    background-size: cover;
    background-repeat: no-repeat;
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
}

/* Container Styling */
.container2 {
    width: 90%;
    max-width: 500px;
    margin: 50px auto;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 10px;
    background-color: #f9f9f9;
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
}

/* Button Styling */
.container2 button {
    background-color: #512da8;
    color: #fff;
    font-size: 14px;
    padding: 12px;
    border: none;
    border-radius: 20px;
    text-transform: uppercase;
    cursor: pointer;
    width: 100%;
    margin-top: 15px;
    transition: background-color 0.3s ease;
}
.container2 button:hover {
    background-color: #4527a0;
}
.container2 button:disabled {
    background-color: #ccc;
    cursor: not-allowed;
}

/* Input Styling */
.container2 input {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    font-size: 16px;
    border: none;
    border-radius: 8px;
    background-color: #eee;
    text-align: center;
    letter-spacing: 2px;
}

/* Success and Error Messages */
.success {
    color: green;
    margin-bottom: 10px;
}
.error {
    color: red;
    margin-bottom: 10px;
}

/* Back Button Styling */
.back-button {
    display: inline-block;
    color: rgb(0, 51, 102);
    font-size: 18px;
    margin-bottom: 10px;
    text-decoration: none;
}
.back-button i {
    font-size: 18px;
}

/* Responsive Styling */
@media only screen and (max-width: 768px) {
    .container2 {
        padding: 15px;
        box-shadow: none;
    }
    .container2 button {
        font-size: 14px;
        padding: 10px;
    }
    .container2 input {
        font-size: 14px;
        padding: 8px;
    }
    .login-box-msg {
        font-size: 18px;
    }
}

@media only screen and (max-width: 480px) {
    .container2 {
        margin: 20px auto;
        padding: 10px;
    }
    .back-button i {
        font-size: 16px;
    }
}
</style>
</body>