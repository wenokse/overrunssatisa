<?php
include 'includes/session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_id'])) {
    $comment_id = $_POST['comment_id'];
    
    $conn = $pdo->open();
    
    try {
        $stmt = $conn->prepare("DELETE FROM comment WHERE id = :comment_id");
        $stmt->execute(['comment_id' => $comment_id]);
        
        echo json_encode(['success' => true, 'message' => 'Comment deleted successfully']);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    
    $pdo->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>