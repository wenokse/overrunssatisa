<?php
include 'includes/session.php';

// Check if either email or contact verification is in progress
if ((!isset($_SESSION['reset_email']) && !isset($_SESSION['reset_contact'])) || !isset($_SESSION['reset_time'])) {
    header('location: password_forgot');
    exit();
}

// Get the verification method and contact info
$is_email = isset($_SESSION['reset_email']);
$contact = $is_email ? $_SESSION['reset_email'] : $_SESSION['reset_contact'];
$expiry = $_SESSION['reset_time'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $otp = $_POST['otp'];
    
    // Check if OTP has expired
    if (time() > $expiry) {
        $_SESSION['error'] = 'OTP has expired. Please request a new one.';
        header('location: ' . ($is_email ? 'password_forgot' : 'another'));
        exit();
    }

    try {
        $conn = $pdo->open();
        
        // Check the OTP against the database
        $stmt = $conn->prepare("SELECT * FROM users WHERE " . ($is_email ? "email" : "contact_info") . " = :contact");
        $stmt->execute(['contact' => $contact]);
        $user = $stmt->fetch();

        if ($user && $user['reset_code'] === $otp) {
            // Store verified contact info for password reset
            if ($is_email) {
                $_SESSION['reset_email_verified'] = $contact;
                unset($_SESSION['reset_email']);
            } else {
                $_SESSION['reset_contact_verified'] = $contact;
                unset($_SESSION['reset_contact']);
            }
            header('location: reset_password');
            exit();
        } else {
            $_SESSION['error'] = 'Invalid OTP';
        }

        $pdo->close();
    } catch(PDOException $e) {
        $_SESSION['error'] = 'Connection error: ' . $e->getMessage();
    }
}

$remaining_time = $expiry - time();
?>

<?php include 'includes/header.php'; ?>
<script src="js/sweetalert.min.js"></script>
<body>
<br><br><br><br>
<div class="container2">
    <a href="<?php echo $is_email ? 'password_forgot' : 'another'; ?>" style="color: rgb(0, 51, 102);">
        <i class="fa fa-arrow-left" style="color: rgb(0, 51, 102);"></i>
    </a>
    <center><h2>Verify OTP</h2></center>
    <p>Please enter the OTP sent to:<br>
    <strong><?php echo htmlspecialchars($contact); ?></strong></p>
    <p>Time remaining: <span id="countdown"></span></p>
    
    <?php
    if (isset($_SESSION['error'])) {
        echo "
            <script>
                swal({
                    title: '". $_SESSION['error'] ."',
                    icon: 'error',
                    button: 'OK'
                });
            </script>
        ";
        unset($_SESSION['error']);
    }
    ?>
    
    <form action="reset_verify" method="POST">
        <div class="form-group">
            <label for="otp">Enter OTP:</label>
            <input type="text" id="otp" name="otp" required 
                   pattern="[0-9]{6}" maxlength="6" 
                   placeholder="Enter 6-digit OTP" 
                   title="Please enter 6-digit OTP">
        </div>
        <button type="submit" id="verifyButton" class="btn btn-primary btn-block">
            <i class="fa fa-check-square-o"></i> Verify OTP
        </button>
    </form>
    
    <p class="text-center">
        <a href="<?php echo $is_email ? 'another' : 'password_forgot'; ?>" class="button">
            Try using <?php echo $is_email ? 'SMS' : 'email'; ?> instead
        </a>
    </p>
</div>

<script>
    // Initialize countdown timer
    var timeLeft = <?php echo $remaining_time; ?>;
    var timerId = setInterval(countdown, 1000);

    function countdown() {
        if (timeLeft <= 0) {
            clearTimeout(timerId);
            document.getElementById("countdown").innerHTML = "Expired";
            document.getElementById("verifyButton").disabled = true;
            swal({
                title: "OTP Expired",
                text: "Please request a new OTP",
                icon: "warning",
                button: "OK"
            }).then(function() {
                window.location.href = "<?php echo $is_email ? 'password_forgot' : 'another'; ?>";
            });
        } else {
            var minutes = Math.floor(timeLeft / 60);
            var seconds = timeLeft % 60;
            document.getElementById("countdown").innerHTML = 
                minutes + "m " + (seconds < 10 ? "0" : "") + seconds + "s";
            timeLeft--;
        }
    }

    // OTP input validation
    document.getElementById('otp').addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
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
    border-radius: 10px;
    background-color: #f9f9f9;
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
    margin-top: 10px;
}
.container2 button:disabled {
    background-color: #ccc;
    cursor: not-allowed;
}
.text-center {
    text-align: center;
    margin-top: 15px;
}
.button {
    color: #512da8;
    text-decoration: none;
}
.button:hover {
    text-decoration: underline;
}
.form-group {
    margin-bottom: 15px;
}
</style>
</body>
</html>