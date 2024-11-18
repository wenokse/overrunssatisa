<?php
// get_address.php
include 'includes/session.php';

header('Content-Type: application/json');

try {
    $conn = $pdo->open();
    $stmt = $conn->prepare("SELECT * FROM delivery_address WHERE user_id=:user_id");
    $stmt->execute(['user_id' => $user['id']]);
    $address = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode($address);
} catch(PDOException $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}

$pdo->close();
?>