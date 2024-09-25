<?php
include 'includes/session.php';

$response = array('success' => false);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $sender_id = $input['sender_id'] ?? null;
    $admin_id = $_SESSION['admin'];

    if ($sender_id && $admin_id) {
        $conn = $pdo->open();
        try {
            $stmt = $conn->prepare("DELETE FROM messages WHERE (sender_id = :sender_id AND receiver_id = :admin_id) OR (sender_id = :admin_id AND receiver_id = :sender_id)");
            $stmt->execute(['sender_id' => $sender_id, 'admin_id' => $admin_id]);
            
            $response['success'] = true;
        } catch(PDOException $e) {
            $response['error'] = $e->getMessage();
        }
        $pdo->close();
    }
}

echo json_encode($response);
?>