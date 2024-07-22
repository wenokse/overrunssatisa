<?php
include 'includes/session.php';
include 'includes/conn.php';

if(isset($_POST['reset'])){
    $email = $_POST['email'];
    $reset_code = $_POST['reset_code'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT *, COUNT(*) AS numrows FROM users WHERE email = :email AND reset_code = :reset_code");
    $stmt->execute(['email' => $email, 'reset_code' => $reset_code]);
    $row = $stmt->fetch();

    if($row['numrows'] > 0){
        $password = password_hash($password, PASSWORD_DEFAULT);

        try{
            $stmt = $conn->prepare("UPDATE users SET password = :password, reset_code = '' WHERE email = :email");
            $stmt->execute(['password' => $password, 'email' => $email]);

            $_SESSION['success'] = 'Password successfully reset';
            header('location: login.php');
        }
        catch(PDOException $e){
            $_SESSION['error'] = $e->getMessage();
        }
    }
    else{
        $_SESSION['error'] = 'Invalid email or reset code';
    }
}
else{
    $_SESSION['error'] = 'Input reset code first';
}

header('location: password_reset.php');
?>