<?php
include 'includes/session.php';

// Ensure the admin login email is set in the session
if (!isset($_SESSION['admin_login_email'])) {
    $_SESSION['error'] = 'Please log in first.';
    header('Location: login');
    exit();
}
function verifyAdminLoginOTP($email, $input_otp) {
    if (!isset($_SESSION['otp'])) {
        return false; // No OTP session exists
    }

    $otp_data = $_SESSION['otp'];

    // Verify email, OTP, and expiry
    if ($otp_data['contact_info'] === $email &&
        hash_equals($otp_data['otp'], $input_otp) && // Use hash_equals to prevent timing attacks
        time() <= $otp_data['expiry']) {
        
        unset($_SESSION['otp']); // Clear OTP after successful verification
        return true;
    }

    return false;
}

// Calculate time remaining for OTP
$is_expired = false;
$remaining_minutes = 0;
$remaining_seconds = 0;

if (isset($_SESSION['otp'])) {
    $time_left = $_SESSION['otp']['expiry'] - time();
    if ($time_left > 0) {
        $remaining_minutes = floor($time_left / 60);
        $remaining_seconds = $time_left % 60;
    } else {
        $is_expired = true;
    }
}
if (isset($_POST['verify_admin_otp'])) {
    // Sanitize and validate OTP input
    $email = filter_var($_SESSION['admin_login_email'], FILTER_SANITIZE_EMAIL);
    $user_otp = filter_input(INPUT_POST, 'verification_code', FILTER_SANITIZE_NUMBER_INT);

    // Additional validation
    if (!$email || !$user_otp || !preg_match('/^\d{6}$/', $user_otp)) {
        $_SESSION['error'] = 'Invalid OTP format.';
        header('Location: admin_otp_verify');
        exit();
    }

    try {
        // Verify the OTP
        $is_verified = verifyAdminLoginOTP($email, $user_otp);

        if ($is_verified) {
            // Fetch user details
            $conn = $pdo->open();
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                throw new Exception('User not found.');
            }

            // Set admin session
            $_SESSION['admin'] = $user['id'];
            $_SESSION['success'] = 'Admin login successful.';
            
            // Clear temporary login session data
            unset($_SESSION['admin_login_email'], $_SESSION['admin_login_firstname']);

            header('Location: admin/home');
            exit();
        } else {
            $_SESSION['error'] = 'Invalid or expired OTP. Please try again.';
            header('Location: admin_otp_verify');
            exit();
        }
    } catch (Exception $e) {
        error_log('Admin OTP Verification Error: ' . $e->getMessage());
        $_SESSION['error'] = 'An unexpected error occurred. Please try again.';
        header('Location: admin_otp_verify');
        exit();
    } finally {
        $pdo->close();
    }
}

// Modify resend OTP part
if (isset($_POST['resend'])) {
    if (isset($_SESSION['last_otp_resend_time']) && 
        time() - $_SESSION['last_otp_resend_time'] < 60) {
        $_SESSION['error'] = 'Please wait a minute before requesting another OTP.';
        header('Location: admin_otp_verify');
        exit();
    }

    $_SESSION['last_otp_resend_time'] = time();
    $email = $_SESSION['admin_login_email'];
    $firstname = $_SESSION['admin_login_firstname'];

    if (sendAdminLoginOTP($email, $firstname)) {
        $_SESSION['success'] = 'OTP resent successfully.';
    } else {
        $_SESSION['error'] = 'Failed to resend OTP. Please try again.';
    }
    header('Location: admin_otp_verify');
    exit();
}

function generateSecureOTP() {
    return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include 'includes/header.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
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
            font-size: 16px;
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

        /* Error Styling */
        .error {
            background-color: #ffebee;
            color: #d32f2f;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
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
</head>
<body>
    <br><br><br><br>
    <div class="container2">
        <a href="login" class="back-button">
            <i class="fa fa-arrow-left"></i>
        </a>
        <h2 class="login-box-msg">Enter Admin Verification Code</h2>
        <b>
            <p>Time remaining: 
                <span id="countdown">
                    <?php echo $is_expired ? "Expired" : $remaining_minutes . "m " . $remaining_seconds . "s"; ?>
                </span>
            </p>
        </b>
        
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
        
        <form action="admin_otp_verify" method="POST">
            <label for="verification_code">Enter Verification Code:</label>
            <input type="text" name="verification_code" required 
                   pattern="[0-9]{6}" 
                   maxlength="6" 
                   placeholder="Enter 6-digit OTP" 
                   title="Please enter 6-digit OTP"
                   autocomplete="one-time-code">
            <button type="submit" name="verify_admin_otp" id="verifyButton">Verify</button>
        </form>
        <!-- <form action="admin_otp_verify" method="POST">
            <button type="submit" name="resend" id="resendButton" class="resend-button" 
                    <?php echo !$is_expired ? 'style="display:none;"' : ''; ?>>Resend Code</button>
        </form> -->
    </div>

    <script src="js/sweetalert.min.js"></script>
    <script>
        // Set the countdown timer if not expired
        var countdownElement = document.getElementById("countdown");
        var timeLeft = <?php echo json_encode($time_left); ?>;

        function updateCountdown() {
            if (timeLeft > 0) {
                var minutes = Math.floor(timeLeft / 60);
                var seconds = timeLeft % 60;
                countdownElement.textContent = minutes + "m " + seconds + "s";
                timeLeft--;
                setTimeout(updateCountdown, 1000);
            } else {
                countdownElement.textContent = "Expired";
            }
        }

        if (timeLeft > 0) {
            updateCountdown();
        }
    </script>
</body>
</html>