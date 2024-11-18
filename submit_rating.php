<?php
include 'includes/session.php';

header('Content-Type: application/json');

if(isset($_POST['product_id']) && isset($_POST['rating']) && isset($_POST['detail_id'])) {
    $product_id = $_POST['product_id'];
    $rating = $_POST['rating'];
    $detail_id = $_POST['detail_id'];
    $user_id = $user['id'];
    $comment = isset($_POST['comment']) ? $_POST['comment'] : '';
    
    $conn = $pdo->open();
    try {
        $conn->beginTransaction();
        
        // Insert rating with date
        $stmt = $conn->prepare("INSERT INTO ratings (user_id, product_id, rating, created_at, updated_at) VALUES (:user_id, :product_id, :rating, NOW(), NOW())");
        $stmt->execute(['user_id' => $user_id, 'product_id' => $product_id, 'rating' => $rating]);
        $rating_id = $conn->lastInsertId();
        
        // Insert comment if provided with date
        if(!empty($comment)) {
            $stmt = $conn->prepare("INSERT INTO comment (product_id, user_id, comment, created_at) VALUES (:product_id, :user_id, :comment, NOW())");
            $stmt->execute(['product_id' => $product_id, 'user_id' => $user_id, 'comment' => $comment]);
        }
        
        // Handle file uploads
        if(isset($_FILES['attachments'])) {
            $upload_dir = 'images/rating_attachments/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // Loop through each uploaded file
            foreach($_FILES['attachments']['tmp_name'] as $key => $tmp_name) {
                $file_name = $_FILES['attachments']['name'][$key];
                $file_size = $_FILES['attachments']['size'][$key];
                $file_tmp = $_FILES['attachments']['tmp_name'][$key];
                $file_type = $_FILES['attachments']['type'][$key];
                
                // Validate file type
                $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'video/mp4', 'video/mpeg', 'video/quicktime'];
                if(!in_array($file_type, $allowed_types)) {
                    continue;
                }
                
                // Validate file size (10MB max)
                if($file_size > 10 * 1024 * 1024) {
                    continue;
                }
                
                // Generate unique filename
                $unique_filename = uniqid() . '_' . $file_name;
                $upload_path = $upload_dir . $unique_filename;
                
                if(move_uploaded_file($file_tmp, $upload_path)) {
                    // Save file information to database with date
                    $stmt = $conn->prepare("INSERT INTO rating_attachments (rating_id, file_name, file_type, created_at) VALUES (:rating_id, :file_name, :file_type, NOW())");
                    $stmt->execute([
                        'rating_id' => $rating_id,
                        'file_name' => $unique_filename,
                        'file_type' => $file_type
                    ]);
                }
            }
        }
        
        // Update detail status
        $stmt = $conn->prepare("UPDATE details SET is_rated = 1 WHERE id = :detail_id");
        $stmt->execute(['detail_id' => $detail_id]);
        
        $conn->commit();
        
        echo json_encode(['success' => true]);
    }
    catch(PDOException $e) {
        $conn->rollBack();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    
    $pdo->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
}
?>
