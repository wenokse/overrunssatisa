<?php
include 'includes/session.php';

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to reply.']);
    exit;
}

$conn = $pdo->open();

$reply = $_POST['reply'];
$parent_id = $_POST['parent_id'];
$user_id = $_SESSION['user'];

try {
    // First, get the product_id from the parent comment
    $stmt = $conn->prepare("SELECT product_id FROM comment WHERE id = :parent_id");
    $stmt->execute(['parent_id' => $parent_id]);
    $parent_comment = $stmt->fetch();
    
    if (!$parent_comment) {
        throw new Exception("Parent comment not found.");
    }
    
    $product_id = $parent_comment['product_id'];

    // Now insert the reply
    $stmt = $conn->prepare("INSERT INTO comment (product_id, user_id, comment, parent_id, created_at) VALUES (:product_id, :user_id, :comment, :parent_id, NOW())");
    $stmt->execute([
        'product_id' => $product_id,
        'user_id' => $user_id,
        'comment' => $reply,
        'parent_id' => $parent_id
    ]);
    
    echo json_encode(['success' => true, 'message' => 'Reply submitted successfully.']);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$pdo->close();
?>