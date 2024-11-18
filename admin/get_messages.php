<?php
include 'includes/session.php';

$admin_id = $_SESSION['admin'];
$conn = $pdo->open();

try {
    // First get the admin type
    $stmt = $conn->prepare("SELECT type FROM users WHERE id = :admin_id");
    $stmt->execute(['admin_id' => $admin_id]);
    $admin = $stmt->fetch();
    
    if ($admin['type'] == 1 || $admin['type'] == 0) {
        // For admin and type 1 users, get all vendors with their latest message (if any)
        $stmt = $conn->prepare("
            WITH LatestMessages AS (
                SELECT 
                    CASE 
                        WHEN sender_id = :admin_id THEN receiver_id
                        ELSE sender_id 
                    END AS user_id,
                    message,
                    timestamp,
                    sender_id,
                    ROW_NUMBER() OVER (
                        PARTITION BY 
                            CASE 
                                WHEN sender_id = :admin_id THEN receiver_id
                                ELSE sender_id 
                            END 
                        ORDER BY timestamp DESC
                    ) as rn
                FROM messages
                WHERE sender_id = :admin_id OR receiver_id = :admin_id
            )
            SELECT 
                u.id AS sender_id,
                u.firstname,
                u.lastname,
                u.photo,
                u.store,
                u.type,
                COALESCE(lm.message, '') AS last_message,
                COALESCE(lm.timestamp, u.created_on) AS timestamp,
                CASE 
                    WHEN lm.sender_id = :admin_id THEN 'sent'
                    WHEN lm.sender_id IS NOT NULL THEN 'received'
                    ELSE ''
                END AS message_type
            FROM users u
            LEFT JOIN LatestMessages lm ON u.id = lm.user_id AND lm.rn = 1
            WHERE u.type = 2
            ORDER BY 
                COALESCE(lm.timestamp, u.created_on) DESC
        ");
        $stmt->execute(['admin_id' => $admin_id]);
    } else {
        // For vendors and other users, show only chatted users
        $stmt = $conn->prepare("
            WITH LatestMessages AS (
                SELECT 
                    CASE 
                        WHEN sender_id = :admin_id THEN receiver_id
                        ELSE sender_id 
                    END AS user_id,
                    message,
                    timestamp,
                    sender_id,
                    ROW_NUMBER() OVER (
                        PARTITION BY 
                            CASE 
                                WHEN sender_id = :admin_id THEN receiver_id
                                ELSE sender_id 
                            END 
                        ORDER BY timestamp DESC
                    ) as rn
                FROM messages
                WHERE sender_id = :admin_id OR receiver_id = :admin_id
            )
            SELECT
                DISTINCT 
                u.id AS sender_id,
                u.firstname,
                u.lastname,
                u.photo,
                u.store,
                u.type,
                COALESCE(lm.message, '') AS last_message,
                COALESCE(lm.timestamp, u.created_on) AS timestamp,
                CASE
                    WHEN lm.sender_id = :admin_id THEN 'sent'
                    WHEN lm.sender_id IS NOT NULL THEN 'received'
                    ELSE ''
                END AS message_type
            FROM users u
            JOIN LatestMessages lm ON u.id = lm.user_id AND lm.rn = 1
            WHERE u.id != :admin_id
            ORDER BY lm.timestamp DESC
        ");
        $stmt->execute(['admin_id' => $admin_id]);
    }
    
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($messages);

} catch(PDOException $e) {
    error_log($e->getMessage());
    echo json_encode([]);
} finally {
    $pdo->close();
}
?>