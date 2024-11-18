<?php
include 'includes/session.php';

if (!isset($_SESSION['user']) || !isset($_GET['sender_id'])) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid request'
    ]);
    exit;
}

try {
    $conn = $pdo->open();
    
    // First get the current user's photo
    $userStmt = $conn->prepare("SELECT photo FROM users WHERE id = :user_id");
    $userStmt->execute(['user_id' => $_SESSION['user']]);
    $userPhoto = $userStmt->fetch(PDO::FETCH_COLUMN);
    
    // Then get the messages with sender information
    $stmt = $conn->prepare("
        SELECT 
            m.*,
            CASE WHEN m.sender_id = :user_id THEN 1 ELSE 0 END as is_sender,
            u.photo as sender_photo,
            u.firstname as sender_firstname,
            u.lastname as sender_lastname
        FROM messages m
        LEFT JOIN users u ON u.id = m.sender_id
        WHERE (m.sender_id = :user_id AND m.receiver_id = :sender_id)
           OR (m.sender_id = :sender_id AND m.receiver_id = :user_id)
        ORDER BY m.timestamp ASC
    ");

    $stmt->execute([
        'user_id' => $_SESSION['user'],
        'sender_id' => $_GET['sender_id']
    ]);

    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Mark messages as read
    $updateStmt = $conn->prepare("
        UPDATE messages 
        SET is_read = 1 
        WHERE sender_id = :sender_id 
        AND receiver_id = :user_id 
        AND is_read = 0
    ");
    
    $updateStmt->execute([
        'sender_id' => $_GET['sender_id'],
        'user_id' => $_SESSION['user']
    ]);

    // Process messages for security and formatting
    foreach ($messages as &$message) {
        $message['message'] = htmlspecialchars($message['message']);
        $message['timestamp_formatted'] = date('M j, Y g:i A', strtotime($message['timestamp']));
        
        // Ensure photo paths are secure
        $message['sender_photo'] = htmlspecialchars($message['sender_photo']);
        $message['user_photo'] = htmlspecialchars($userPhoto);
    }

    echo json_encode([
        'success' => true,
        'messages' => $messages
    ]);

} catch (PDOException $e) {
    error_log($e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Database error occurred'
    ]);
} finally {
    $pdo->close();
}
?>