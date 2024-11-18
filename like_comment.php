<?php
header('Content-Type: application/json');
include 'includes/session.php';

if (!isset($_SESSION['user'])) {
    echo json_encode([
        'success' => false,
        'error' => 'User must be logged in'
    ]);
    exit;
}

if (!isset($_POST['review_id']) || !isset($_POST['action'])) {
    echo json_encode([
        'success' => false,
        'error' => 'Missing required parameters'
    ]);
    exit;
}

$review_id = $_POST['review_id'];
$user_id = $user['id'];
$action = $_POST['action'];

if (!in_array($action, ['like', 'dislike'])) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid action'
    ]);
    exit;
}

$conn = $pdo->open();

try {
    $conn->beginTransaction();

    // Check if user already has an action on this comment
    $stmt = $conn->prepare("
        SELECT action 
        FROM comment_likes 
        WHERE user_id = :user_id 
        AND comment_id = :comment_id
    ");
    $stmt->execute([
        'user_id' => $user_id,
        'comment_id' => $review_id
    ]);
    $existing_action = $stmt->fetch(PDO::FETCH_COLUMN);

    if ($existing_action) {
        if ($existing_action === $action) {
            // Remove the action if clicking the same button
            $stmt = $conn->prepare("
                DELETE FROM comment_likes 
                WHERE user_id = :user_id 
                AND comment_id = :comment_id
            ");
            $stmt->execute([
                'user_id' => $user_id,
                'comment_id' => $review_id
            ]);

            // Update comment counts
            $stmt = $conn->prepare("
                UPDATE comment 
                SET " . ($action === 'like' ? 'likes' : 'dislikes') . " = " . 
                    ($action === 'like' ? 'likes' : 'dislikes') . " - 1 
                WHERE id = :comment_id
            ");
            $stmt->execute(['comment_id' => $review_id]);
        } else {
            // Change the action if clicking different button
            $stmt = $conn->prepare("
                UPDATE comment_likes 
                SET action = :action 
                WHERE user_id = :user_id 
                AND comment_id = :comment_id
            ");
            $stmt->execute([
                'action' => $action,
                'user_id' => $user_id,
                'comment_id' => $review_id
            ]);

            // Update comment counts for both actions
            $stmt = $conn->prepare("
                UPDATE comment 
                SET likes = likes " . ($action === 'like' ? '+ 1' : '- 1') . ",
                    dislikes = dislikes " . ($action === 'like' ? '- 1' : '+ 1') . "
                WHERE id = :comment_id
            ");
            $stmt->execute(['comment_id' => $review_id]);
        }
    } else {
        // Insert new action
        $stmt = $conn->prepare("
            INSERT INTO comment_likes (user_id, comment_id, action) 
            VALUES (:user_id, :comment_id, :action)
        ");
        $stmt->execute([
            'user_id' => $user_id,
            'comment_id' => $review_id,
            'action' => $action
        ]);

        // Update comment counts
        $stmt = $conn->prepare("
            UPDATE comment 
            SET " . ($action === 'like' ? 'likes' : 'dislikes') . " = " . 
                ($action === 'like' ? 'likes' : 'dislikes') . " + 1 
            WHERE id = :comment_id
        ");
        $stmt->execute(['comment_id' => $review_id]);
    }

    $conn->commit();

    echo json_encode([
        'success' => true
    ]);

} catch (PDOException $e) {
    $conn->rollBack();
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}

$pdo->close();
?>