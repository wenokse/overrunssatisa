<?php
include 'includes/session.php';

$admin_id = $_SESSION['admin'];
$conn = $pdo->open();

$stmt = $conn->prepare("
    SELECT
        CASE
            WHEN m.sender_id = :admin_id THEN m.receiver_id
            ELSE m.sender_id
        END AS sender_id,
        u.firstname,
        u.lastname,
        u.photo,
        m.message AS last_message,
        m.timestamp,
        CASE
            WHEN m.sender_id = :admin_id THEN 'sent'
            ELSE 'received'
        END AS message_type
    FROM messages m
    JOIN users u ON u.id = CASE WHEN m.sender_id = :admin_id THEN m.receiver_id ELSE m.sender_id END
    WHERE m.id = (
        SELECT MAX(m2.id)
        FROM messages m2
        WHERE (m2.sender_id = :admin_id AND m2.receiver_id = u.id)
           OR (m2.sender_id = u.id AND m2.receiver_id = :admin_id)
    )
    ORDER BY m.timestamp DESC
");
$stmt->execute(['admin_id' => $admin_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($messages);

$pdo->close();
?>