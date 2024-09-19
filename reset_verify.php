<?php
include 'includes/session.php';

if (!isset($_SESSION['reset_email']) || !isset($_SESSION['reset_time'])) {
    header('location: password_forgot.php');
    exit();
}

$email = $_SESSION['reset_email'];
$expiry = $_SESSION['reset_time'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $otp = $_POST['otp'];
    
    // Check if OTP has expired
    if (time() > $expiry) {
        $_SESSION['error'] = 'OTP has expired. Please request a new one.';
        header('location: password_forgot.php');
        exit();
    }

    $conn = $pdo->open();
    $stmt = $conn->prepare("SELECT reset_code FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user) {
        $stored_otp = $user['reset_code'];

        // Compare the entered OTP with the stored OTP
        if ($otp === $stored_otp) {
            // OTP is correct
            $_SESSION['reset_email'] = $email;
            header('location: reset_password.php');
            exit();
        } else {
            $_SESSION['error'] = 'Invalid OTP';
        }
    } else {
        $_SESSION['error'] = 'Email not found.';
    }

    $pdo->close();
}

$remaining_time = $expiry - time();
?>

<?php include 'includes/header.php'; ?>
<script src="js/sweetalert.min.js"></script>
<body>
<br><br><br><br>
<div class="container2">
    <center><h2>Verify OTP</h2></center>
    <p>Time remaining: <span id="countdown"></span></p>
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
    <form action="reset_verify.php" method="POST">
        <label for="otp">Enter OTP:</label>
        <input type="text" id="otp" name="otp" required>
        <button type="submit" id="verifyButton" class="btn btn-primary btn-block " <i class="fa fa-check-square-o"></i>>Verify OTP</button>
    </form>
</div>
</body>
<script>
        var timeLeft = <?php echo $remaining_time; ?>;
        var timerId = setInterval(countdown, 1000);

        function countdown() {
            if (timeLeft == 0) {
                clearTimeout(timerId);
                document.getElementById("countdown").innerHTML = "Expired";
                document.getElementById("verifyButton").disabled = true;
            } else {
                var minutes = Math.floor(timeLeft / 60);
                var seconds = timeLeft % 60;
                document.getElementById("countdown").innerHTML = minutes + "m " + seconds + "s";
                timeLeft--;
            }
        }
    </script>
<style>
  body{
    background: rgb(0, 51, 102);
    background-size: cover;
    background-repeat: no-repeat;
  }
  .container2 { 
    width: 500px;
    height: 260px;
    margin: 0 auto 50px;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 10px;
    background-color: #f9f9f9;
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
    align: center;
    
  }
  .container2 input{
    background-color: #eee;
    border: none;
    margin: 8px 0;
    padding: 10px 15px;
    font-size: 13px;
    border-radius: 8px;
    width: 100%;
    outline: none;
}
.container2 button{
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
}

  
  </style>
</html>
