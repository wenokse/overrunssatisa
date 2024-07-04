<?php
include 'includes/session.php';
$conn = $pdo->open();

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['rating']) && isset($data['product_id'])) {
    $rating = $data['rating'];
    $product_id = $data['product_id'];
    $user_id = $_SESSION['user'];
    $now = date('Y-m-d');

    try {
        
        // Insert or update the rating
        $stmt = $conn->prepare("INSERT INTO ratings (user_id, product_id, rating, created_at) VALUES (:user_id, :product_id, :rating, :created_at) ON DUPLICATE KEY UPDATE rating = :rating");
        $stmt->execute(['user_id' => $user_id, 'product_id' => $product_id, 'rating' => $rating, 'created_at' => $now]);

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
}

$pdo->close();
?>
