<?php
session_start();
include 'includes/conn.php';

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to perform this action.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user'];
    $comment_id = $_POST['comment_id'];
    $action = $_POST['action'];

    $conn = $pdo->open();

    try {
        $conn->beginTransaction();
        $stmt = $conn->prepare("SELECT * FROM comment_likes WHERE user_id = :user_id AND comment_id = :comment_id");
        $stmt->execute(['user_id' => $user_id, 'comment_id' => $comment_id]);
        $existing_action = $stmt->fetch();

        if ($existing_action) {
            if ($existing_action['action'] == $action) {
                $stmt = $conn->prepare("DELETE FROM comment_likes WHERE user_id = :user_id AND comment_id = :comment_id");
                $stmt->execute(['user_id' => $user_id, 'comment_id' => $comment_id]);
                $update_field = $action == 'like' ? 'likes = likes - 1' : 'dislikes = dislikes - 1';
                $message = 'Action removed';
            } else {
                $stmt = $conn->prepare("UPDATE comment_likes SET action = :action WHERE user_id = :user_id AND comment_id = :comment_id");
                $stmt->execute(['action' => $action, 'user_id' => $user_id, 'comment_id' => $comment_id]);
                $update_field = $action == 'like' ? 'likes = likes + 1, dislikes = dislikes - 1' : 'likes = likes - 1, dislikes = dislikes + 1';
                $message = 'Action updated';
            }
        } else {
            $stmt = $conn->prepare("INSERT INTO comment_likes (user_id, comment_id, action) VALUES (:user_id, :comment_id, :action)");
            $stmt->execute(['user_id' => $user_id, 'comment_id' => $comment_id, 'action' => $action]);
            $update_field = $action == 'like' ? 'likes = likes + 1' : 'dislikes = dislikes + 1';
            $message = 'Action added';
        }

        $stmt = $conn->prepare("UPDATE comment SET $update_field WHERE id = :comment_id");
        $stmt->execute(['comment_id' => $comment_id]);
        $conn->commit();

        echo json_encode(['success' => true, 'message' => $message]);
    } catch(PDOException $e) {
        $conn->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }

    $pdo->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>