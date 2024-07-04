<?php
include 'includes/session.php';

$conn = $pdo->open();

$response = array('success' => false, 'comments' => ''); 

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    try {
        $stmt = $conn->prepare("SELECT comment.*, users.firstname, users.lastname FROM comment LEFT JOIN users ON users.id = comment.user_id WHERE product_id = :product_id ORDER BY created_at DESC");
        $stmt->execute(['product_id' => $product_id]);
        $comments = $stmt->fetchAll(); 
        

        if ($comments) {
            $response['success'] = true;

            foreach ($comments as $comment) {
                // // Calculate the time difference
                // $currentTime = new DateTime();
                // $commentTime = new DateTime($comment['created_at']);
                // $interval = $currentTime->diff($commentTime);
            
                // if ($interval->y > 0) {
                //     $timeAgo = $interval->y . ' year' . ($interval->y > 1 ? 's' : '') . ' ago';
                // } elseif ($interval->m > 0) {
                //     $timeAgo = $interval->m . ' month' . ($interval->m > 1 ? 's' : '') . ' ago';
                // } elseif ($interval->d > 0) {
                //     $timeAgo = $interval->d . ' day' . ($interval->d > 1 ? 's' : '') . ' ago';
                // } elseif ($interval->h > 0) {
                //     $timeAgo = $interval->h . ' hour' . ($interval->h > 1 ? 's' : '') . ' ago';
                // } elseif ($interval->i > 0) {
                //     $timeAgo = $interval->i . ' minute' . ($interval->i > 1 ? 's' : '') . ' ago';
                // } else {
                //     $timeAgo = 'just now';
                // }
                 $formattedDateTime = (new DateTime($comment['created_at']))->format('Y-m-d H:i');

            
                $response['comments'] .= '<div class="comment-container" style="border: 1px solid #ddd; padding: 10px; margin-bottom: 10px; border-radius: 5px; box-shadow: 0 0 5px rgba(0,0,0,0.1);">
                                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                                                <div>
                                                    <b>' . htmlspecialchars($comment['firstname'] . ' ' . $comment['lastname']) . '</b>
                                                    <small style="color: #888;">' . htmlspecialchars($formattedDateTime) . '</small>
                                                </div>
                                                <div class="comment-actions" style="display: flex; align-items: center; gap: 10px;">
                                                    <button class="like-btn" data-comment-id="' . $comment['id'] . '">&#128077; ' . $comment['likes'] . '</button>
                                                    <button class="dislike-btn" data-comment-id="' . $comment['id'] . '">&#128078; ' . $comment['dislikes'] . '</button>
                                                </div>
                                            </div>
                                            <p style="margin: 0;">' . htmlspecialchars($comment['comment']) . '</p>
                                          </div>';
            }
            
            
        }
    } catch (PDOException $e) {
        $response['message'] = 'There was an error fetching comments: ' . $e->getMessage(); 
    }
}

$pdo->close();
echo json_encode($response);
?>


