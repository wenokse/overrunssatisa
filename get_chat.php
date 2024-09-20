<?php
include 'includes/session.php';

if (isset($_GET['sender_id']) && isset($_SESSION['user'])) {
    $user_id = $_SESSION['user'];
    $sender_id = $_GET['sender_id'];

    try {
        $conn = $pdo->open();

        // Fetch messages
        $stmt = $conn->prepare("
            SELECT 
                m.*,
                u.firstname,
                u.lastname,
                COALESCE(u.photo, 'profile.jpg') AS photo
            FROM messages m 
            JOIN users u ON m.sender_id = u.id 
            WHERE (m.sender_id = :sender_id AND m.receiver_id = :user_id) 
               OR (m.sender_id = :user_id AND m.receiver_id = :sender_id)
            ORDER BY m.timestamp ASC
        ");
        $stmt->bindParam(':sender_id', $sender_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Process messages to ensure correct data
        foreach ($messages as &$message) {
            $message['firstname'] = htmlspecialchars($message['firstname']);
            $message['lastname'] = htmlspecialchars($message['lastname']);
        }

        // Update messages as read
        $stmt = $conn->prepare("
            UPDATE messages 
            SET is_read = 1 
            WHERE sender_id = :sender_id AND receiver_id = :user_id AND is_read = 0
        ");
        $stmt->bindParam(':sender_id', $sender_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        // Output messages
        header('Content-Type: application/json');
        echo json_encode($messages);

    } catch (PDOException $e) {
        error_log($e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Database error']);
    } finally {
        $pdo->close();
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request']);
}
?>