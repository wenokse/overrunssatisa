<?php
include 'includes/session.php';
$conn = $pdo->open();
if(isset($_POST['email'])){
    $email = $_POST['email'];
    try{
        $stmt = $conn->prepare("SELECT COUNT(*) AS numrows FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();
        if($row['numrows'] > 0){
            $_SESSION['success'] = 'Success';
            header('location: Phone.php');
            exit(); 
        }
        else{
            $_SESSION['error'] = 'Email not found';
            header('location: login.php');
            exit(); 
        }
    }
    catch(PDOException $e){
        echo "There is some problem in connection: " . $e->getMessage();
    }
}
else {
    header('location: login.php');
    exit(); 
}
$pdo->close();
?>
