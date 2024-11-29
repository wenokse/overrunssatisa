<?php
include 'includes/session.php';

// Ensure the admin login email is set in the session
if (!isset($_SESSION['admin_login_email'])) {
    $_SESSION['error'] = 'Please log in first.';
    header('Location: login');
    exit();
}

function verifyAdminLoginOTP($contact_info, $input_otp) {
    if (!isset($_SESSION['otp'])) {
        return false; // No OTP session exists
    }

    $otp_data = $_SESSION['otp'];

    // Verify contact info, OTP, and expiry
    if ($otp_data['contact_info'] === $contact_info &&
        $otp_data['otp'] === $input_otp &&
        time() <= $otp_data['expiry']) {
        
        unset($_SESSION['otp']); // Clear OTP after successful verification
        return true;
    }

    return false;
}

// Handle OTP verification
if (isset($_POST['verify_admin_otp'])) {
    // Sanitize and validate OTP input
    $contact_info = filter_var($_SESSION['admin_login_contact'], FILTER_SANITIZE_STRING);
    $user_otp = filter_input(INPUT_POST, 'otp', FILTER_SANITIZE_NUMBER_INT);

    // Additional validation
    if (!$contact_info || !$user_otp || !preg_match('/^\d{6}$/', $user_otp)) {
        $_SESSION['error'] = 'Invalid OTP format.';
        header('Location: admin_otp_verify');
        exit();
    }

    try {
        // Verify the OTP
        $is_verified = verifyAdminLoginOTP($contact_info, $user_otp);

        if ($is_verified) {
            // Fetch user details
            $conn = $pdo->open();
            $stmt = $conn->prepare("SELECT * FROM users WHERE contact_info = :contact_info");
            $stmt->execute(['contact_info' => $contact_info]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                throw new Exception('User not found.');
            }

            // Set admin session
            $_SESSION['admin'] = $user['id'];
            $_SESSION['success'] = 'Admin login successful.';
            
            // Clear temporary login session data
            unset($_SESSION['admin_login_email'], $_SESSION['admin_login_contact']);

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
?>

<?php include 'includes/header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>

    <style>
        .otp-verify-box {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .alert-danger {
            color: red;
            margin-bottom: 15px;
        }
        body {
    font-family: 'Arial', sans-serif;
    background-color: #f4f6f9;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
}

.container {
    width: 100%;
    display: flex;
    justify-content: center;
}

.otp-verify-box {
    background-color: #ffffff;
    max-width: 450px;
    width: 100%;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    text-align: center;
}

h2 {
    color: #333;
    margin-bottom: 20px;
    font-weight: 600;
}

.alert-danger {
    background-color: #ffebee;
    color: #d32f2f;
    padding: 10px;
    border-radius: 4px;
    margin-bottom: 20px;
    font-size: 14px;
}

.form-group {
    margin-bottom: 20px;
    text-align: left;
}

label {
    display: block;
    margin-bottom: 8px;
    color: #555;
    font-weight: 500;
}

input[type="text"] {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 16px;
    transition: border-color 0.3s ease;
}

input[type="text"]:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
}

.btn {
    width: 100%;
    padding: 12px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s ease;
}

.btn:hover {
    background-color: #0056b3;
}

.text-muted {
    color: #6c757d;
    text-decoration: none;
    font-size: 14px;
    transition: color 0.3s ease;
}

.text-muted:hover {
    color: #007bff;
    text-decoration: underline;
}

@media (max-width: 480px) {
    .otp-verify-box {
        margin: 20px;
        padding: 20px;
    }
}
    </style>
</head>
<body>
    <div class="container">
        <div class="otp-verify-box">
            <h2 class="text-center">Admin Login Verification</h2>
            
            <?php
            if(isset($_SESSION['error'])){
                echo "
                <div class='alert alert-danger text-center'>
                    ".$_SESSION['error']."
                </div>
                ";
                unset($_SESSION['error']);
            }
            ?>
            
            <form method="POST" action="admin_otp_verify">
                <div class="form-group">
                    <label for="otp">Enter OTP sent to your registered mobile number</label>
                    <input type="text" name="otp" id="otp" class="form-control" 
                           placeholder="Enter 6-digit OTP" 
                           required 
                           pattern="\d{6}" 
                           maxlength="6"
                           title="6-digit OTP">
                </div>
                
                <button type="submit" name="verify_admin_otp" class="btn btn-primary btn-block mt-3">
                    Verify OTP
                </button>
            </form>
            
            <div class="text-center mt-3">
                <a href="resend_admin_otp" class="text-muted">Didn't receive OTP? Resend</a>
            </div>
        </div>
    </div>
</body>
</html>