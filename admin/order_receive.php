<?php
include 'includes/session.php';

if (isset($_GET['sale_id'])) {
    $conn = $pdo->open();
    
    try {
        // Validate sale_id
        $sale_id = $_GET['sale_id'];
        
        // First check if a rider exists for this sale
        $stmt = $conn->prepare("SELECT COUNT(*) FROM rider WHERE sales_id = :sale_id");
        $stmt->execute(['sale_id' => $sale_id]);
        $has_rider = $stmt->fetchColumn() > 0;
        
        if (!$has_rider) {
            $_SESSION['error'] = 'Cannot proceed with delivery. No rider assigned.';
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }
        
        // Check current status to ensure it's at status 2 (pickup)
        $stmt = $conn->prepare("SELECT status FROM sales WHERE id = :sale_id");
        $stmt->execute(['sale_id' => $sale_id]);
        $current_status = $stmt->fetchColumn();
        
        if ($current_status != 2) {
            $_SESSION['error'] = 'Invalid order status for delivery.';
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }
        
        // If all checks pass, update the status
        $stmt = $conn->prepare("UPDATE sales SET status = 3 WHERE id = :sale_id");
        $stmt->execute(['sale_id' => $sale_id]);
        
       
        
    } catch(PDOException $e) {
        $_SESSION['error'] = 'Error updating order status: ' . $e->getMessage();
    }
    
    $pdo->close();
    
} else {
    $_SESSION['error'] = 'Invalid sale ID.';
}

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();
?>