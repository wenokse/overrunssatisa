<?php
include 'includes/session.php';

$conn = $pdo->open();

$response = array('success' => false, 'comments' => '', 'debug' => '');

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    try {
        // Fetch all comments for the product, including replies
        $stmt = $conn->prepare("
            SELECT c.*, u.firstname, u.lastname 
            FROM comment c 
            LEFT JOIN users u ON u.id = c.user_id 
            WHERE c.product_id = :product_id 
            ORDER BY c.parent_id ASC, c.created_at ASC
        ");
        $stmt->execute(['product_id' => $product_id]);
        $allComments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $response['debug'] .= "Query executed. Number of comments: " . count($allComments) . "\n";

        if ($allComments) {
            $response['success'] = true;
            $commentTree = buildCommentTree($allComments);
            $response['comments'] = renderCommentTree($commentTree);
        } else {
            $response['debug'] .= "No comments found for product ID: " . $product_id . "\n";
        }
    } catch (PDOException $e) {
        $response['message'] = 'There was an error fetching comments: ' . $e->getMessage();
        $response['debug'] .= "PDO Exception: " . $e->getMessage() . "\n";
    }
} else {
    $response['debug'] .= "Invalid request or missing product_id\n";
}

$pdo->close();
echo json_encode($response);

function buildCommentTree($comments, $parentId = 0) {
    $branch = array();

    foreach ($comments as $comment) {
        if ($comment['parent_id'] == $parentId) {
            $children = buildCommentTree($comments, $comment['id']);
            if ($children) {
                $comment['replies'] = $children;
            }
            $branch[] = $comment;
        }
    }

    return $branch;
}

function renderCommentTree($comments, $level = 0) {
    $html = '';
    foreach ($comments as $comment) {
        $html .= renderComment($comment, $level);
        if (isset($comment['replies'])) {
            $replyCount = count($comment['replies']);
            if ($replyCount > 0) {
                $html .= "<div class='view-replies' style='margin-left: " . (($level + 1) * 20) . "px; margin-bottom: 10px;'>
                            <a href='#' class='view-replies-link' data-comment-id='" . $comment['id'] . "' data-reply-count='" . $replyCount . "'>
                                View " . $replyCount . " " . ($replyCount == 1 ? "reply" : "replies") . "
                            </a>
                          </div>";
                $html .= "<div class='replies-container' id='replies-" . $comment['id'] . "' style='display: none;'>";
                $html .= renderCommentTree($comment['replies'], $level + 1);
                $html .= "</div>";
            }
        }
    }
    return $html;
}


function renderComment($comment, $level) {
    $formattedDateTime = (new DateTime($comment['created_at']))->format('Y-m-d H:i');
    $margin = $level * 20; // Increase left margin for nested comments

    $html = "
    <div class='comment-container' style='border: 1px solid #ddd; padding: 10px; margin-bottom: 10px; margin-left: {$margin}px; border-radius: 5px; box-shadow: 0 0 5px rgba(0,0,0,0.1);'>
        <div style='display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;'>
            <div>
                <b>" . htmlspecialchars($comment['firstname'] . ' ' . $comment['lastname']) . "</b>
                <small style='color: #888;'>" . htmlspecialchars($formattedDateTime) . "</small>
            </div>
            <div class='comment-actions' style='display: flex; align-items: center; gap: 10px;'>
                <button class='like-btn' data-comment-id='" . $comment['id'] . "'>&#128077; " . $comment['likes'] . "</button>
                <button class='dislike-btn' data-comment-id='" . $comment['id'] . "'>&#128078; " . $comment['dislikes'] . "</button>
                <button class='reply-btn' data-comment-id='" . $comment['id'] . "'>Reply</button>
            </div>
        </div>
        <p style='margin: 0;'>" . htmlspecialchars($comment['comment']) . "</p>
        <div class='reply-form' style='display:none; margin-top: 10px;'>
            <textarea class='form-control reply-text' rows='2' style='width: 100%; margin-bottom: 5px;'></textarea>
            <button class='btn btn-primary submit-reply' data-parent-id='" . $comment['id'] . "'>Submit Reply</button>
        </div>
    </div>";

    return $html;
}
?>