<?php
include 'includes/session.php';

$conn = $pdo->open();

$response = array('success' => false, 'message' => '');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_SESSION['user']) && !empty($_POST['comment'])) {
        $user_id = $_SESSION['user'];
        $product_id = $_POST['product_id'];
        $comment = $_POST['comment'];

        try {
            $stmt = $conn->prepare("INSERT INTO comment (user_id, product_id, comment, created_at) VALUES (:user_id, :product_id, :comment, NOW())");
            $stmt->execute(['user_id' => $user_id, 'product_id' => $product_id, 'comment' => $comment]);

            $response['success'] = true;
            $response['message'] = 'Comment submitted successfully.';
        } catch (PDOException $e) {
            $response['message'] = 'There was an error submitting your comment: ' . $e->getMessage();
        }
    } else {
        $response['message'] = 'Please log in to submit a comment.';
        $response['redirect'] = 'login.php'; 
    }
} else {
    $response['message'] = 'Invalid request.';
}

$pdo->close();
echo json_encode($response);
?>
