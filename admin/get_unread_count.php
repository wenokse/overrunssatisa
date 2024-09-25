<?php
include 'includes/session.php'; 
if (isset($_SESSION['admin'])) {
    $admin_id = $_SESSION['admin'];
    
    try {
        $conn = $pdo->open();  
        $stmt = $conn->prepare("
            SELECT COUNT(*) AS unread_count 
            FROM messages 
            WHERE receiver_id = :admin_id AND is_read = 0
        ");
        $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $unread_count = $result['unread_count'] ?? 0;  
        echo json_encode(['unread_count' => $unread_count]);
        
    } catch (PDOException $e) {
        error_log($e->getMessage());
        echo json_encode(['unread_count' => 0]);
    } finally {
        $pdo->close();  
    }
} else {
    echo json_encode(['unread_count' => 0]);
}
?>
