<?php
include 'includes/session.php';

if(isset($_POST['id'])){
    $id = $_POST['id'];

    $conn = $pdo->open();

    try {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        echo json_encode(['success' => true]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }

    $pdo->close();
} else {
    echo json_encode(['success' => false, 'message' => 'No ID provided']);
}
?>
