<?php
include 'includes/session.php';
// Handle reset code from the URL
if (isset($_GET['code'])) {
    $reset_code = $_GET['code'];

    try {
        $conn = $pdo->open();

        // Retrieve the email and reset code where the code hasn't expired
        $stmt = $conn->prepare("
            SELECT email, reset_code 
            FROM users 
            WHERE reset_code_expiry > :current_time
        ");
        $stmt->execute(['current_time' => time()]);

        $is_valid = false;
        while ($row = $stmt->fetch()) {
            // Secure comparison to prevent timing attacks
            if (hash_equals($row['reset_code'], $reset_code)) {
                $_SESSION['reset_email_verified'] = $row['email'];
                $is_valid = true;

                // Clear the reset code and expiry after verification
                $update = $conn->prepare("
                    UPDATE users 
                    SET reset_code = NULL, reset_code_expiry = NULL 
                    WHERE email = :email
                ");
                $update->execute(['email' => $row['email']]);
                break;
            }
        }

        if (!$is_valid) {
            $_SESSION['error'] = 'Invalid or expired reset link.';
            header('location: password_forgot');
            exit();
        }
    } catch (PDOException $e) {
        error_log("Database Error: " . $e->getMessage());
        $_SESSION['error'] = 'System error occurred. Please try again later.';
        header('location: password_forgot');
        exit();
    } finally {
        $pdo->close();
    }

    header('location: reset_password');
    exit();
}


// Validate if verification session is set
if ((!isset($_SESSION['reset_email']) && !isset($_SESSION['reset_phone'])) || !isset($_SESSION['reset_time'])) {
    $_SESSION['error'] = 'Invalid verification attempt. Please start over.';
    header('location: password_forgot');
    exit();
}

// Determine verification method and contact information
$is_email = isset($_SESSION['reset_email']);
$contact = $is_email ? $_SESSION['reset_email'] : $_SESSION['reset_phone'];
$expiry = $_SESSION['reset_time'];

// Check if OTP is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $otp = trim($_POST['otp']);

    // Validate OTP format
    if (!preg_match('/^[0-9]{6}$/', $otp)) {
        $_SESSION['error'] = 'Invalid OTP format. Please enter 6 digits.';
        header('location: reset_verify');
        exit();
    }

    // Check OTP expiration
    if (time() > $expiry) {
        $_SESSION['error'] = 'OTP has expired. Please request a new one.';
        unset($_SESSION['reset_email'], $_SESSION['reset_phone'], $_SESSION['reset_time']);
        header('location: password_forgot');
        exit();
    }

    try {
        $conn = $pdo->open();

        // Define the field to check based on the method
        $field = $is_email ? "email" : "contact_info";
        $stmt = $conn->prepare("
            SELECT id FROM users 
            WHERE $field = :contact AND reset_code = :otp AND reset_code_expiry > :current_time
        ");
        $stmt->execute([
            'contact' => $contact,
            'otp' => $otp,
            'current_time' => time()
        ]);

        if ($stmt->rowCount() > 0) {
            // Set the verification flag in session
            if ($is_email) {
                $_SESSION['reset_email_verified'] = $contact;
            } else {
                $_SESSION['reset_contact_verified'] = $contact;
            }
            
            // Clear temporary session data
            unset($_SESSION['reset_email'], $_SESSION['reset_phone'], $_SESSION['reset_time']);
            
            // Update database to clear the used OTP
            $updateStmt = $conn->prepare("
                UPDATE users 
                SET reset_code = NULL, reset_code_expiry = NULL 
                WHERE $field = :contact
            ");
            $updateStmt->execute(['contact' => $contact]);
            
            header('location: reset_password');
            exit();
        } else {
            $_SESSION['error'] = 'Invalid or expired OTP. Please try again.';
        }
    } catch (PDOException $e) {
        error_log("OTP Verification Error: " . $e->getMessage());
        $_SESSION['error'] = 'System error occurred. Please try again later.';
    }

    $pdo->close();
    header('location: reset_verify');
    exit();
}

// Calculate remaining OTP time
$remaining_time = max(0, $expiry - time());
?>
<?php include 'includes/header.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <script src="js/sweetalert.min.js"></script>
    <title>Verify OTP - Password Reset</title>
</head>
<body>
<br><br><br><br>
<div class="container2">
    <a href="password_forgot" style="color: rgb(0, 51, 102);">
        <i class="fa fa-arrow-left" style="color: rgb(0, 51, 102);"></i>
    </a>
    
    <center><h2>Verify OTP</h2></center>
    
    <div class="info-box">
        <p>Please enter the OTP sent to:<br>
        <strong><?php echo htmlspecialchars($contact); ?></strong></p>
        <p>Time remaining: <span id="countdown" class="countdown"></span></p>
    </div>
    
    <?php
    if (isset($_SESSION['error'])) {
        echo "
            <script>
                swal({
                    title: '". htmlspecialchars($_SESSION['error']) ."',
                    icon: 'error',
                    button: 'OK'
                });
            </script>
        ";
        unset($_SESSION['error']);
    }
    ?>
    
    <form action="reset_verify" method="POST" id="otpForm">
        <div class="form-group">
            <label for="otp">Enter OTP:</label>
            <input type="text" 
                   id="otp" 
                   name="otp" 
                   required 
                   pattern="[0-9]{6}" 
                   maxlength="6" 
                   placeholder="Enter 6-digit OTP" 
                   title="Please enter 6-digit OTP"
                   autocomplete="one-time-code">
        </div>
        <button type="submit" id="verifyButton" class="btn btn-primary btn-block">
            <i class="fa fa-check-square-o"></i> Verify OTP
        </button>
    </form>
</div>

<script>
// Initialize countdown timer
const initialTime = <?php echo $remaining_time; ?>;
let timeLeft = initialTime;
const timerId = setInterval(countdown, 1000);

function countdown() {
    if (timeLeft <= 0) {
        clearInterval(timerId);
        document.getElementById("countdown").innerHTML = "Expired";
        document.getElementById("verifyButton").disabled = true;
        document.getElementById("otp").disabled = true;
        
        swal({
            title: "OTP Expired",
            text: "Please request a new OTP",
            icon: "warning",
            button: "OK"
        }).then(function() {
            window.location.href = "password_forgot";
        });
    } else {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        document.getElementById("countdown").innerHTML = 
            `${minutes}m ${seconds.toString().padStart(2, '0')}s`;
        timeLeft--;
    }
}

// OTP input validation and formatting
const otpInput = document.getElementById('otp');
otpInput.addEventListener('input', function(e) {
    // Remove non-numeric characters
    let value = this.value.replace(/\D/g, '');
    // Limit to 6 digits
    value = value.substring(0, 6);
    this.value = value;
    
    // Enable/disable submit button based on input length
    document.getElementById('verifyButton').disabled = value.length !== 6;
});

// Form submission validation
document.getElementById('otpForm').addEventListener('submit', function(e) {
    const otp = otpInput.value;
    if (otp.length !== 6 || !/^\d+$/.test(otp)) {
        e.preventDefault();
        swal({
            title: "Invalid OTP",
            text: "Please enter a 6-digit number",
            icon: "error",
            button: "OK"
        });
    }
});
</script>

<style>
body {
    background: rgb(0, 51, 102);
    background-size: cover;
    background-repeat: no-repeat;
}
.container2 { 
    width: 500px;
    height: auto;
    min-height: 300px;
    margin: 0 auto 50px;
    padding: 20px;
    border: 1px solid #ccc;
    border: 1px solid #ccc;
        border-radius: 20px;
        background: linear-gradient(135deg, #6e8efb, #a777e3);
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
}
.info-box {
    background-color: #f8f9fa;
    border-left: 4px solid #512da8;
    padding: 15px;
    margin: 15px 0;
    border-radius: 4px;
}
.countdown {
    font-weight: bold;
    color: #512da8;
}
.container2 input {
    background-color: #eee;
    border: none;
    margin: 8px 0;
    padding: 10px 15px;
    font-size: 16px;
    border-radius: 8px;
    width: 100%;
    outline: none;
    text-align: center;
    letter-spacing: 2px;
}
.container2 button {
    background-color: #512da8;
    color: #fff;
    font-size: 14px;
    padding: 12px 45px;
    border: 1px solid transparent;
    border-radius: 20px;
    font-weight: 600;
    letter-spacing: 0.5px;
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
.text-center {
    text-align: center;
    margin-top: 20px;
}
.button {
    color: #512da8;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s ease;
}
.button:hover {
    color: #4527a0;
    text-decoration: underline;
}
.form-group {
    margin-bottom: 15px;
}
/* Responsive Design */
@media (max-width: 768px) {
    .container2 {
        width: 90%;
        margin: 20px auto;
        padding: 15px;
    }

    .reset-option i {
        font-size: 20px;
    }

    .reset-form input {
        padding: 8px;
    }

    .submit-btn, .back-btn {
        padding: 8px;
        font-size: 14px;
    }
}

@media (max-width: 480px) {
    .container2 {
        width: 95%;
        padding: 10px;
    }

    .reset-option i {
        font-size: 18px;
    }

    .reset-form input {
        padding: 6px;
    }

    .submit-btn, .back-btn {
        padding: 6px;
        font-size: 12px;
    }
}
</style>

</body>
</html>