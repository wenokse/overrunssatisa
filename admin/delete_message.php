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
            $stmt = $conn->prepare("DELETE FROM messages WHERE id = :message_id AND (sender_id = :admin_id OR receiver_id = :admin_id)");
            $stmt->execute(['message_id' => $message_id, 'admin_id' => $admin_id]);
            
            if ($stmt->rowCount() > 0) {
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