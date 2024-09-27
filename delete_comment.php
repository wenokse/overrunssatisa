<?php
include 'includes/session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_id'])) {
    $comment_id = $_POST['comment_id'];
    
    // Check if the user is logged in and is the owner of the comment or an admin
    if (isset($_SESSION['user'])) {
        $conn = $pdo->open();
        
        try {
            $stmt = $conn->prepare("SELECT user_id FROM comments WHERE id = :comment_id");
            $stmt->execute(['comment_id' => $comment_id]);
            $comment = $stmt->fetch();
            
            if ($comment && ($_SESSION['user'] == $comment['user_id'] || $_SESSION['user'] == 1)) {
                $stmt = $conn->prepare("DELETE FROM comments WHERE id = :comment_id");
                $stmt->execute(['comment_id' => $comment_id]);
                
                echo json_encode(['success' => true, 'message' => 'Comment deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'You are not authorized to delete this comment']);
            }
        } catch(PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        
        $pdo->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'You must be logged in to delete a comment']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>