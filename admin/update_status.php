<?php
// update_status.php
include 'includes/session.php';

if(isset($_GET['sale_id']) && isset($_GET['status'])) {
    $sale_id = $_GET['sale_id'];
    $status = $_GET['status'];
    
    $conn = $pdo->open();
    
    try {
        $stmt = $conn->prepare("UPDATE sales SET status = :status WHERE id = :sale_id");
        $stmt->execute(['status' => $status, 'sale_id' => $sale_id]);
        
        $_SESSION['success'] = 'Order status updated successfully';
    }
    catch(PDOException $e) {
        $_SESSION['error'] = $e->getMessage();
    }
    
    $pdo->close();
}

header('location: sales');
?>