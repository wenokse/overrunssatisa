<?php
include 'includes/session.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['admin'])) {
    $admin_id = $_SESSION['admin'];  
 
    $input = json_decode(file_get_contents('php://input'), true);
    $receiver_id = $input['receiver_id'] ?? null;
    $message_content = trim($input['message'] ?? '');

    if ($receiver_id && !empty($message_content)) {
        try {
            $conn = $pdo->open();  

            $stmt = $conn->prepare("
                INSERT INTO messages (sender_id, receiver_id, message, timestamp, is_read) 
                VALUES (:sender_id, :receiver_id, :message, NOW(), 0)
            ");
            $stmt->bindParam(':sender_id', $admin_id, PDO::PARAM_INT);
            $stmt->bindParam(':receiver_id', $receiver_id, PDO::PARAM_INT);
            $stmt->bindParam(':message', $message_content, PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => true, 'message' => 'Message sent successfully.']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to send message.']);
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            echo json_encode(['success' => false, 'error' => 'An error occurred.']);
        } finally {
            $pdo->close();
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid input.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Unauthorized request.']);
}
?>
