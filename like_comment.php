<?php
include 'includes/session.php';

$response = array('success' => false);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $comment_id = $_POST['comment_id'];
    $action = $_POST['action'];

    try {
        if ($action == 'like') {
            $stmt = $conn->prepare("UPDATE comment SET likes = likes + 1 WHERE id = :comment_id");
        } elseif ($action == 'dislike') {
            $stmt = $conn->prepare("UPDATE comment SET dislikes = dislikes + 1 WHERE id = :comment_id");
        }
        $stmt->execute(['comment_id' => $comment_id]);
        $response['success'] = true;
    } catch (PDOException $e) {
        $response['message'] = 'There was an error: ' . $e->getMessage();
    }
}

echo json_encode($response);
?>
