<?php
include 'includes/session.php';


if (isset($_GET['code']) && isset($_GET['email'])) {
    $reset_code = $_GET['code'];
    $email = $_GET['email'];

    try {
        $conn = $pdo->open();

        $stmt = $conn->prepare("
            SELECT email, reset_code 
            FROM users 
            WHERE email = :email 
            AND reset_code_expiry > :current_time
        ");
        $stmt->execute([
            'email' => $email,
            'current_time' => time()
        ]);

        $row = $stmt->fetch();
        
        if ($row && password_verify($reset_code, $row['reset_code'])) {
            $_SESSION['reset_email_verified'] = $row['email'];
            
            // Clear reset code after verification
            $update = $conn->prepare("
                UPDATE users 
                SET reset_code = NULL, reset_code_expiry = NULL 
                WHERE email = :email
            ");
            $update->execute(['email' => $row['email']]);
        } else {
            $_SESSION['error'] = 'Invalid or expired reset link.';
            header('location: password_forgot');
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Database error: ' . $e->getMessage();
        header('location: password_forgot');
        exit();
    }

    $pdo->close();
    header('location: reset_password');
    exit();
}

// Keep your existing verification check
if (!isset($_SESSION['reset_email_verified']) && !isset($_SESSION['reset_contact_verified'])) {
    $_SESSION['error'] = 'Please verify your identity first.';
    header('location: password_forgot');
    exit();
}

// Get the verified contact info (either email or phone)
$contact_info = isset($_SESSION['reset_email_verified']) ? 
                $_SESSION['reset_email_verified'] : 
                $_SESSION['reset_contact_verified'];

$is_email = isset($_SESSION['reset_email_verified']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $_SESSION['error'] = 'Passwords do not match';
    } else {
        try {
            $conn = $pdo->open();
            
            // Update password based on verification method
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            
            $sql = "UPDATE users SET 
                    password = :password, 
                    reset_code = NULL, 
                    reset_code_expiry = NULL 
                    WHERE " . ($is_email ? "email" : "contact_info") . " = :contact";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                'password' => $password_hash, 
                'contact' => $contact_info
            ]);

            if ($stmt->rowCount() > 0) {
                $_SESSION['success'] = 'Password reset successfully';
                // Clear all reset-related session variables
                unset($_SESSION['reset_email_verified']);
                unset($_SESSION['reset_contact_verified']);
                unset($_SESSION['reset_time']);
                header('location: login');
                exit();
            } else {
                $_SESSION['error'] = 'Failed to reset password';
            }
        } catch(PDOException $e) {
            $_SESSION['error'] = 'Connection error: ' . $e->getMessage();
        }
        $pdo->close();
    }
}
?>
<?php include 'includes/header.php'; ?>
<script src="js/sweetalert.min.js"></script>
<body>
<br><br><br><br>
<div class="container2">
    <a href="<?php echo $is_email ? 'password_forgot' : 'another'; ?>" style="color: rgb(0, 51, 102);">
        <i class="fa fa-arrow-left" style="color: rgb(0, 51, 102);"></i>
    </a>
    <center><h2>Reset Password</h2></center>
    <p>Resetting password for: <strong><?php echo htmlspecialchars($contact_info); ?></strong></p>
    
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
    
    <form action="reset_password" method="POST" onsubmit="return validateForm()">
        <div class="form-group">
            <label for="password">New Password:</label>
            <div class="password-container">
                <input type="password" id="password" name="password" 
                       required minlength="8" 
                       placeholder="Enter new password">
                <i class="fa fa-eye password-toggle" onclick="togglePassword('password')"></i>
            </div>
            <small class="password-requirements">
                Password must be at least 8 characters long
            </small>
        </div>
        
        <div class="form-group">
            <label for="confirm_password">Confirm New Password:</label>
            <div class="password-container">
                <input type="password" id="confirm_password" name="confirm_password" 
                       required minlength="8"
                       placeholder="Confirm new password">
                <i class="fa fa-eye password-toggle" onclick="togglePassword('confirm_password')"></i>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary btn-block">
            <i class="fa fa-check-square-o"></i> Reset Password
        </button>
    </form>
</div>

<script>
function validateForm() {
    var password = document.getElementById('password').value;
    var confirmPassword = document.getElementById('confirm_password').value;
    
    if (password !== confirmPassword) {
        swal({
            title: "Passwords don't match!",
            text: "Please make sure both passwords are identical",
            icon: "error",
            button: "OK"
        });
        return false;
    }
    
    if (password.length < 8) {
        swal({
            title: "Password too short!",
            text: "Password must be at least 8 characters long",
            icon: "error",
            button: "OK"
        });
        return false;
    }
    
    return true;
}

function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = input.parentElement.querySelector('.password-toggle');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
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
    min-height: 350px;
    margin: 0 auto 50px;
    padding: 20px;
    border: 1px solid #ccc;
    border: 1px solid #ccc;
        border-radius: 20px;
        background: linear-gradient(135deg, #6e8efb, #a777e3);
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
}
.container2 input {
    background-color: #eee;
    border: none;
    margin: 8px 0;
    padding: 10px 15px;
    font-size: 13px;
    border-radius: 8px;
    width: 100%;
    outline: none;
}
.container2 button {
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
    width: 100%;
    margin-top: 20px;
}
.form-group {
    margin-bottom: 15px;
}
.password-requirements {
    color: #666;
    font-size: 12px;
    margin-top: 5px;
    display: block;
}
.button:hover {
    opacity: 0.9;
}
.password-container {
    position: relative;
}
.password-toggle {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #666;
    padding: 5px;
}
.password-toggle:hover {
    color: #512da8;
}
</style>
</body>
</html>