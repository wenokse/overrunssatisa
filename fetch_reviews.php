<?php
header('Content-Type: application/json');
include 'includes/session.php';

if(!isset($_GET['product_id'])) {
    echo json_encode([
        'success' => false,
        'error' => 'Product ID is required'
    ]);
    exit;
}

$product_id = $_GET['product_id'];
$user_id = isset($_SESSION['user']) ? $user['id'] : null;

$conn = $pdo->open();
try {

    $color_stmt = $conn->prepare("
        SELECT color FROM product_colors 
        WHERE product_id = :product_id LIMIT 1
    ");
    $color_stmt->execute(['product_id' => $product_id]);
    $purchased_color = $color_stmt->fetchColumn();

    // Fetch reviews with all related data including user actions
    $stmt = $conn->prepare("
        SELECT r.*, ra.file_name, ra.file_type, c.id as comment_id, 
               c.comment, c.likes, c.dislikes,
               u.firstname, u.lastname, u.photo as user_photo,
               CASE WHEN cl.action = 'like' THEN 1 ELSE 0 END as user_liked,
               CASE WHEN cl.action = 'dislike' THEN 1 ELSE 0 END as user_disliked
        FROM ratings r
        LEFT JOIN rating_attachments ra ON r.id = ra.rating_id
        LEFT JOIN comment c ON c.product_id = r.product_id AND c.user_id = r.user_id
        LEFT JOIN users u ON r.user_id = u.id
        LEFT JOIN comment_likes cl ON c.id = cl.comment_id AND cl.user_id = :user_id
        WHERE r.product_id = :product_id
        ORDER BY r.created_at DESC
    ");
    
    $stmt->execute([
        'product_id' => $product_id,
        'user_id' => $user_id
    ]);
    
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $output = '';
    foreach($reviews as $review) {
        $user_photo = !empty($review['user_photo']) ? 'images/'.$review['user_photo'] : 'images/profile.jpg';
        $rating_stars = '';
        for($i = 1; $i <= 5; $i++) {
            $rating_stars .= ($i <= $review['rating']) ? 
                '<i class="fa fa-star text-warning"></i>' : 
                '<i class="fa fa-star-o text-warning"></i>';
        }

        $output .= '
        <div class="review-box">
            <div class="review-header">
                <div class="user-info">
                    <img src="'.$user_photo.'" class="user-photo" alt="User Photo">
                    <div class="user-details">
                        <h4>'.htmlspecialchars($review['firstname'].' '.$review['lastname']).'</h4>
                        <div class="rating">'.$rating_stars.'</div>
                        <span class="review-date">'.date('M d, Y', strtotime($review['created_at'])).'</span><br>
                         <span class="review-date">Color: '.htmlspecialchars($purchased_color).'</span>
                    </div>
                </div>
            </div>
            <div class="review-content">
                '.(!empty($review['comment']) ? '<p class="review-text">'.htmlspecialchars($review['comment']).'</p>' : '').'
            </div>';

            if (!empty($review['file_name'])) {
                $file_path = 'images/rating_attachments/' . htmlspecialchars($review['file_name']);
                if (in_array($review['file_type'], ['image/jpeg', 'image/png', 'image/gif'])) {
                    $output .= '<div class="review-attachment">
                                    <img src="' . $file_path . '" class="attachment-preview" 
                                         onclick="viewAttachment(\'' . $file_path . '\')" 
                                         alt="Review attachment">
                                </div>';
                } elseif (strpos($review['file_type'], 'video/') !== false) {
                    $output .= '<div class="review-attachment">
                                    <video controls class="video-preview">
                                        <source src="' . $file_path . '" type="' . $review['file_type'] . '">
                                        Your browser does not support the video tag.
                                    </video>
                                </div>';
                }
            }
        

        // Add like/dislike section with user action status
        if(!empty($review['comment_id'])) {
            $liked_class = $review['user_liked'] ? 'active' : '';
            $disliked_class = $review['user_disliked'] ? 'active' : '';
            
            $output .= '
            <div class="review-footer">
                <button class="btn btn-sm like-btn '.$liked_class.'" 
                        data-comment-id="'.$review['comment_id'].'">
                    <i class="fa fa-thumbs-up"></i> 
                    <span class="like-count">'.intval($review['likes']).'</span>
                </button>
                <button class="btn btn-sm dislike-btn '.$disliked_class.'" 
                        data-comment-id="'.$review['comment_id'].'">
                    <i class="fa fa-thumbs-down"></i> 
                    <span class="dislike-count">'.intval($review['dislikes']).'</span>
                </button>
            </div>';
        }
        
        $output .= '</div>';
    }

    if(empty($output)) {
        $output = '<div class="no-reviews">No reviews yet. Be the first to review this product!</div>';
    }
    $output .= '
    <div id="attachmentModal" class="modal">
        <span class="close" onclick="closeModal()">&times;</span>
        <img class="modal-content" id="modalImage">
    </div>
    
    <style>
        /* Modal background styling */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; 
            z-index: 1000; 
            padding-top: 60px;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.8); /* Semi-transparent background */
        }
        
        /* Image inside the modal */
        .modal-content {
            display: block;
            margin: auto;
            max-width: 80%;
            max-height: 80%;
            border-radius: 8px;
        }
    
        /* Close button styling */
        .close {
            position: absolute;
            top: 20px;
            right: 35px;
            color: #ffffff;
            font-size: 35px;
            font-weight: bold;
            cursor: pointer;
            text-shadow: 0 1px 5px rgba(0, 0, 0, 0.5);
            transition: color 0.2s ease;
        }
    
        .close:hover,
        .close:focus {
            color: #ff5555; /* Highlighted color on hover */
            text-decoration: none;
        }
    </style>
    
    <script>
        function viewAttachment(src) {
            var modal = document.getElementById("attachmentModal");
            var modalImage = document.getElementById("modalImage");
            modal.style.display = "block";
            modalImage.src = src;
        }
    
        function closeModal() {
            var modal = document.getElementById("attachmentModal");
            modal.style.display = "none";
        }
    
        // Close modal when clicking outside of the image
        window.onclick = function(event) {
            var modal = document.getElementById("attachmentModal");
            if (event.target === modal) {
                modal.style.display = "none";
            }
        }
    </script>';
    

    echo json_encode([
        'success' => true,
        'reviews' => $output
    ]);

} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}

$pdo->close();
?>
