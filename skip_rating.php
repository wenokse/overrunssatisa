<?php
include 'includes/session.php';

if(isset($_POST['detail_id'])) {
    $detail_id = $_POST['detail_id'];
    
    $conn = $pdo->open();
    try {
        $stmt = $conn->prepare("UPDATE details SET is_rated = 2 WHERE id = :detail_id");
        $stmt->execute(['detail_id' => $detail_id]);
        
        echo json_encode(['success' => true]);
    }
    catch(PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    
    $pdo->close();
}
?>