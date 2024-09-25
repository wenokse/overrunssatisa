<?php
include 'includes/session.php';

$response = array('success' => false);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $message_id = $input['message_id'] ?? null;
    $admin_id = $_SESSION['admin'];

    if ($message_id && $admin_id) {
        $conn = $pdo->open();
        try {
            // First, check if the message was sent by the admin
            $stmt = $conn->prepare("SELECT * FROM messages WHERE id = :message_id AND sender_id = :admin_id");
            $stmt->execute(['message_id' => $message_id, 'admin_id' => $admin_id]);
            
            if ($stmt->rowCount() > 0) {
                $update_stmt = $conn->prepare("UPDATE messages SET message = 'Unsent Message' WHERE id = :message_id");
                $update_stmt->execute(['message_id' => $message_id]);
                $response['success'] = true;
            }
        } catch(PDOException $e) {
            $response['error'] = $e->getMessage();
        }
        $pdo->close();
    }
}

echo json_encode($response);
?>