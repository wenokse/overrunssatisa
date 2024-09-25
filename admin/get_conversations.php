<?php
include 'includes/session.php';

$admin_id = $_SESSION['admin'] ?? null;

if (!$admin_id) {
    echo json_encode([]);
    exit;
}

try {
    $conn = $pdo->open();
    
    // Fetch conversations for the admin
    $stmt = $conn->prepare("
        SELECT u.id, u.firstname, u.lastname, u.photo, m.message AS last_message
        FROM users u
        LEFT JOIN messages m ON m.id = (
            SELECT id FROM messages 
            WHERE (sender_id = u.id AND receiver_id = :admin_id)
               OR (receiver_id = u.id AND sender_id = :admin_id)
            ORDER BY created_at DESC LIMIT 1
        )
        WHERE u.id IN (
            SELECT sender_id FROM messages WHERE receiver_id = :admin_id
            UNION
            SELECT receiver_id FROM messages WHERE sender_id = :admin_id
        )
        ORDER BY m.created_at DESC
    ");
    $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
    $stmt->execute();
    
    $conversations = [];
    foreach ($stmt as $row) {
        $conversations[] = [
            'id' => $row['id'],
            'name' => $row['firstname'] . ' ' . $row['lastname'],
            'photo' => !empty($row['photo']) ? '../images/' . htmlspecialchars($row['photo']) : '../images/profile.jpg',
            'last_message' => htmlspecialchars($row['last_message']),
        ];
    }
    
    $pdo->close();
    
    echo json_encode($conversations);

} catch (PDOException $e) {
    error_log($e->getMessage());
    echo json_encode([]);
    exit;
}
?>
