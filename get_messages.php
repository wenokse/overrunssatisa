<?php
include 'includes/session.php';

if (isset($_SESSION['user'])) {
    $user_id = $_SESSION['user'];

    try {
        $conn = $pdo->open();

        $stmt = $conn->prepare("
            SELECT
                CASE
                    WHEN m.sender_id = :user_id THEN m.receiver_id
                    ELSE m.sender_id
                END AS sender_id,
                u.firstname,
                u.lastname,
                COALESCE(u.photo, 'profile.jpg') AS photo,
                m.message AS last_message,
                m.timestamp,
                CASE
                    WHEN m.sender_id = :user_id THEN 'sent'
                    ELSE 'received'
                END AS message_type
            FROM messages m
            JOIN users u ON u.id = CASE WHEN m.sender_id = :user_id THEN m.receiver_id ELSE m.sender_id END
            WHERE m.id = (
                SELECT MAX(m2.id)
                FROM messages m2
                WHERE (m2.sender_id = :user_id AND m2.receiver_id = u.id)
                   OR (m2.sender_id = u.id AND m2.receiver_id = :user_id)
            )
            ORDER BY m.timestamp DESC
        ");
        $stmt->execute(['user_id' => $user_id]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Process messages to ensure correct data
        foreach ($messages as &$message) {
            $message['firstname'] = htmlspecialchars($message['firstname']);
            $message['lastname'] = htmlspecialchars($message['lastname']);
        }

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