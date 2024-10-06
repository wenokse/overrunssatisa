<?php
include 'includes/session.php';

$conn = null;
$stmt = null;

try {
    $conn = $pdo->open();

    if(isset($_POST['login'])) {
        
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];

        if(!$email || !$password) {
            throw new Exception('Invalid input');
        }

        $stmt = $conn->prepare("SELECT *, COUNT(*) AS numrows FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row && $row['numrows'] > 0) {
            if($row['status'] == 1) { 
                if(password_verify($password, $row['password'])) {
                    setSessionVariables($row);
                     
                    $redirect = $row['type'] ? 'admin/home.php' : 'profile.php';
                    header("Location: $redirect");
                    exit();
                } else {
                    $_SESSION['error'] = 'Incorrect Email and Password';
                }
            } elseif ($row['status'] == 3) {
                $_SESSION['error'] = 'Please wait for admin approval.';
            } elseif ($row['status'] == 0) {
                $_SESSION['error'] = 'Account Deactivated.';
            }
        } else {
            $_SESSION['error'] = 'Email Not Found. Please sign up first.';
            header('Location: signup.php');
            exit();
        }
    } else {
        $_SESSION['error'] = 'Input login credentials first';
    }
} catch(PDOException $e) {
    $_SESSION['error'] = 'Database error occurred. Please try again later.';
   
    error_log('Database error in login: ' . $e->getMessage());
} catch(Exception $e) {
    $_SESSION['error'] = 'An error occurred. Please try again.';
   
    error_log('Error in login: ' . $e->getMessage());
} finally {
   
    if($stmt) {
        $stmt = null;
    }
    if($conn) {
        $pdo->close();
    }
}


header('Location: login.php');
exit();

function setSessionVariables($userData) {
    $_SESSION[$userData['type'] ? 'admin' : 'user'] = $userData['id'];
    $_SESSION['success'] = 'Login successful';
}
?>