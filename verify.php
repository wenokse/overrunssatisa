<?php
include 'includes/session.php';
$conn = $pdo->open();

if(isset($_POST['login'])){
    $email = $_POST['email'];
    $password = $_POST['password'];

    try{
        $stmt = $conn->prepare("SELECT *, COUNT(*) AS numrows FROM users WHERE email = :email");
        $stmt->execute(['email'=>$email]);
        $row = $stmt->fetch();
        if($row['numrows'] > 0){
            if($row['status']){
                if(password_verify($password, $row['password'])){
                    if($row['type']){
                        $_SESSION['admin'] = $row['id'];
                        $_SESSION['success'] = 'Admin Login successfully';
                        header('location: admin/home.php');
                    }
                    else{
                        $_SESSION['user'] = $row['id'];
                        $_SESSION['success'] = 'Login successfully';
                        header('location: profile.php');
                    }
                    exit();
                }
                else{
                    $_SESSION['error'] = 'Incorrect Password';
                }
            }
            else{
                $_SESSION['error'] = 'Account Deactivated.';
            }
        }
        else{
            $_SESSION['error'] = 'Email not found';
        }
    }
    catch(PDOException $e){
        $_SESSION['error'] = 'There is some problem in connection: ' . $e->getMessage();
    }
}
else{
    $_SESSION['error'] = 'Input login credentials first';
}

$pdo->close();

header('location: login.php');
?>