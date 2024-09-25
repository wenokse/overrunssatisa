<?php
include 'includes/session.php'; 
if (isset($_GET['sender_id']) && isset($_SESSION['admin'])) {
    $admin_id = $_SESSION['admin'];
    $sender_id = $_GET['sender_id'];
    try {
        $conn = $pdo->open(); 
        $stmt = $conn->prepare("
            SELECT m.*, u.firstname, u.lastname, u.photo 
            FROM messages m 
            LEFT JOIN users u ON m.sender_id = u.id 
            WHERE (m.sender_id = :sender_id AND m.receiver_id = :admin_id) 
            OR (m.sender_id = :admin_id AND m.receiver_id = :sender_id)
            ORDER BY m.timestamp ASC
        ");
        $stmt->bindParam(':sender_id', $sender_id, PDO::PARAM_INT);
        $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt->execute();
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $conn->prepare("
            UPDATE messages 
            SET is_read = 1 
            WHERE sender_id = :sender_id AND receiver_id = :admin_id AND is_read = 0
        ");
        $stmt->bindParam(':sender_id', $sender_id, PDO::PARAM_INT);
        $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt->execute(); 

        echo json_encode($messages);
        
    } catch (PDOException $e) {
  
        error_log($e->getMessage());
        echo json_encode([]);
    } finally {
        $pdo->close();  
    }
} else {
    echo json_encode([]);
}
?>
