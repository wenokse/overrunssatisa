<?php
include 'includes/session.php';
$conn = $pdo->open();

if(isset($_POST['contact_info'])){
    $contact_info = $_POST['contact_info'];
    try{
        $stmt = $conn->prepare("SELECT email FROM users WHERE contact_info = :contact_info");
        $stmt->execute(['contact_info' => $contact_info]);
        $row = $stmt->fetch();

        if($row){ // If a row is found
            $_SESSION['success'] = 'Success';
            $_SESSION['reset_email'] = $row['email']; // Store the email in session for password change page
            header('location: password_change.php');
            exit(); 
        }
        else{
            $_SESSION['error'] = 'Contact info not found';
            header('location: login.php');
            exit(); 
        }
    }
    catch(PDOException $e){
        echo "There is some problem in connection: " . $e->getMessage();
    }
}

$pdo->close();
header('location: login.php');
exit(); 
?>
