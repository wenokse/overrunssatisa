<?php
include 'includes/session.php';

if (!isset($_SESSION['user'])) {
    echo json_encode(['unread_count' => 0]);
    exit();
}

$user_id = $_SESSION['user'];

try {
    $conn = $pdo->open();
    
    $stmt = $conn->prepare("
        SELECT COUNT(*) as unread_count
        FROM messages
        WHERE receiver_id = :user_id
        AND is_read = 0
    ");
    
    $stmt->execute(['user_id' => $user_id]);
    $result = $stmt->fetch();
    
    echo json_encode(['unread_count' => (int)$result['unread_count']]);
} catch(PDOException $e) {
    echo json_encode(['unread_count' => 0, 'error' => $e->getMessage()]);
}

$pdo->close();
?>