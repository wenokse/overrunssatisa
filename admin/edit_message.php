<?php
include 'includes/session.php';

$response = array('success' => false);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $message_id = $input['message_id'] ?? null;
    $new_message = $input['new_message'] ?? null;
    $admin_id = $_SESSION['admin'];

    if ($message_id && $new_message && $admin_id) {
        $conn = $pdo->open();
        try {
            $stmt = $conn->prepare("UPDATE messages SET message = :new_message WHERE id = :message_id AND sender_id = :admin_id");
            $stmt->execute(['new_message' => $new_message, 'message_id' => $message_id, 'admin_id' => $admin_id]);
            
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