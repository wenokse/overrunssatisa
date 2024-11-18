<?php
include 'includes/session.php';

$conn = $pdo->open();

if (isset($_SESSION['user'])) {
    try {
        // Delete the address from the database
        $stmt = $conn->prepare("DELETE FROM delivery_address WHERE user_id=:user_id");
        $stmt->execute(['user_id' => $user['id']]);

        echo json_encode(['error' => false, 'message' => 'Address deleted successfully']);
    } catch (PDOException $e) {
        echo json_encode(['error' => true, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => true, 'message' => 'User not logged in']);
}

$pdo->close();
?>
