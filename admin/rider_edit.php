<?php
include 'includes/session.php';

if(isset($_POST['edit'])){
    $id = $_POST['id'];
    $fullname = $_POST['fullname'];
    $contact_info = $_POST['contact_info'];

    $conn = $pdo->open();

    try{
        $stmt = $conn->prepare("UPDATE rider SET fullname=:fullname, contact_info=:contact_info WHERE id=:id");
        $stmt->execute([
            'fullname' => $fullname,
            'contact_info' => $contact_info,
            'id' => $id
        ]);
        $_SESSION['success'] = 'Rider updated successfully';
    }
    catch(PDOException $e){
        $_SESSION['error'] = $e->getMessage();
    }

    $pdo->close();
}
else{
    $_SESSION['error'] = 'Fill up edit rider form first';
}

header('location: rider.php');
?>
