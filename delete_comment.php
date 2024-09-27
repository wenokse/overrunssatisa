<?php
include 'includes/session.php';

$response = array('success' => false, 'message' => '');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_SESSION['user']) && isset($_POST['comment_id'])) {
        $user_id = $_SESSION['user'];
        $comment_id = $_POST['comment_id'];

        try {
            // Ensure the comment belongs to the user or they are an admin
            $stmt = $conn->prepare("DELETE FROM comment WHERE id = :comment_id AND user_id = :user_id");
            $stmt->execute(['comment_id' => $comment_id, 'user_id' => $user_id]);

            if ($stmt->rowCount()) {
                $response['success'] = true;
                $response['message'] = 'Comment deleted successfully.';
            } else {
                $response['message'] = 'You cannot delete this comment.';
            }
        } catch (PDOException $e) {
            $response['message'] = 'Error deleting comment: ' . $e->getMessage();
        }
    } else {
        $response['message'] = 'Unauthorized access.';
    }
} else {
    $response['message'] = 'Invalid request.';
}

echo json_encode($response);
?>
