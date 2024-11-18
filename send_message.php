<?php
include 'includes/session.php';

// Check if user is logged in and request is POST
if (!isset($_SESSION['user'])) {
    echo json_encode([
        'success' => false,
        'error' => 'User not authenticated'
    ]);
    exit;
}

// Get and validate POST data
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['receiver_id']) || !isset($input['message']) || 
    empty(trim($input['message'])) || !is_numeric($input['receiver_id'])) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid input data'
    ]);
    exit;
}

$receiver_id = (int)$input['receiver_id'];
$message = trim($input['message']);
$sender_id = $_SESSION['user'];

// Validate message length
if (strlen($message) > 1000) { // Adjust max length as needed
    echo json_encode([
        'success' => false,
        'error' => 'Message too long'
    ]);
    exit;
}

try {
    $conn = $pdo->open();

    // Verify receiver exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE id = :receiver_id");
    $stmt->execute(['receiver_id' => $receiver_id]);
    if (!$stmt->fetch()) {
        throw new Exception('Invalid receiver');
    }

    // Insert the message
    $stmt = $conn->prepare("
        INSERT INTO messages (sender_id, receiver_id, message, timestamp, is_read) 
        VALUES (:sender_id, :receiver_id, :message, NOW(), 0)
    ");

    $success = $stmt->execute([
        'sender_id' => $sender_id,
        'receiver_id' => $receiver_id,
        'message' => $message
    ]);

    if ($success) {
        // Get the inserted message details for confirmation
        $messageId = $conn->lastInsertId();
        $stmt = $conn->prepare("
            SELECT 
                m.*,
                u.firstname,
                u.lastname,
                u.photo
            FROM messages m
            JOIN users u ON u.id = m.sender_id
            WHERE m.id = :message_id
        ");
        $stmt->execute(['message_id' => $messageId]);
        $messageData = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'message' => [
                'id' => $messageData['id'],
                'timestamp' => $messageData['timestamp'],
                'message' => htmlspecialchars($messageData['message']),
                'sender' => [
                    'firstname' => htmlspecialchars($messageData['firstname']),
                    'lastname' => htmlspecialchars($messageData['lastname']),
                    'photo' => htmlspecialchars($messageData['photo'])
                ]
            ]
        ]);
    } else {
        throw new Exception('Failed to send message');
    }

} catch (Exception $e) {
    error_log($e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Failed to send message'
    ]);
} finally {
    $pdo->close();
}
?>