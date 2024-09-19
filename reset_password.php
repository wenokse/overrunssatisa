<?php
include 'includes/session.php';

if (!isset($_SESSION['reset_email'])) {
    header('location: password_forgot.php');
    exit();
}

$email = $_SESSION['reset_email'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $_SESSION['error'] = 'Passwords do not match';
    } else {
        $conn = $pdo->open();
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = :password, reset_code = NULL, reset_code_expiry = NULL WHERE email = :email");
        $stmt->execute(['password' => $password_hash, 'email' => $email]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['success'] = 'Password reset successfully';
            unset($_SESSION['reset_email']);
            header('location: login.php');
            exit();
        } else {
            $_SESSION['error'] = 'Failed to reset password';
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
    <center><h2>Reset Password</h2></center>
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
    <form action="reset_password.php" method="POST">
        <label for="password">New Password:</label>
        <input type="password" id="password" name="password" required>
        <label for="confirm_password">Confirm New Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" required>
        <button type="submit" class="btn btn-primary btn-block " <i class="fa fa-check-square-o"></i>>Reset Password</button>
    </form>
</div>

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
  </body>