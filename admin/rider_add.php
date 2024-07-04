<?php
include 'includes/session.php';

if(isset($_POST['add'])){
    $fullname = $_POST['fullname'];
    $contact_info = $_POST['contact_info'];
    $status = 1; // Assuming 1 is for active status
    $created_on = date('Y-m-d');
    
    $conn = $pdo->open();

    $filename = $_FILES['photo']['name'];
    if(!empty($filename)){
        $target_dir = "../images/";
        $target_file = $target_dir . basename($filename);
        move_uploaded_file($_FILES['photo']['tmp_name'], $target_file);
    } else {
        $filename = "profile.jpg"; // Default profile image
    }

    try {
        // Check for duplicate contact_info
        $stmt = $conn->prepare("SELECT COUNT(*) FROM rider WHERE contact_info = :contact_info");
        $stmt->execute([':contact_info' => $contact_info]);
        $contact_count = $stmt->fetchColumn();
        
        if($contact_count > 0){
            $_SESSION['error'] = 'Contact information already exists';
            header('location: rider.php');
            exit();
        }

        // Check for duplicate fullname
        $stmt = $conn->prepare("SELECT COUNT(*) FROM rider WHERE fullname = :fullname");
        $stmt->execute([':fullname' => $fullname]);
        $name_count = $stmt->fetchColumn();
        
        if($name_count > 0){
            $_SESSION['error'] = 'Name already exists';
            header('location: rider.php');
            exit();
        }

        // Insert the new rider
        $stmt = $conn->prepare("INSERT INTO rider (fullname, contact_info, photo, status, created_on) VALUES (:fullname, :contact_info, :photo, :status, :created_on)");
        $stmt->execute([
            ':fullname' => $fullname,
            ':contact_info' => $contact_info,
            ':photo' => $filename,
            ':status' => $status,
            ':created_on' => $created_on
        ]);
        $_SESSION['success'] = 'Rider added successfully';
    } catch(PDOException $e) {
        $_SESSION['error'] = $e->getMessage();
    }

    $pdo->close();
} else {
    $_SESSION['error'] = 'Fill up rider form first';
}

header('location: rider.php');
exit();
?>
