<?php
include 'includes/session.php';

if (isset($_POST['parent_id']) && isset($_POST['reply'])) {
    $parent_id = $_POST['parent_id']; // The ID of the original comment
    $reply = $_POST['reply']; // The reply content
    $user_id = $_SESSION['admin']; // The ID of the currently logged-in user (admin or shop owner)
    $now = date('Y-m-d H:i:s'); // Current timestamp

    // Database connection
    $conn = $pdo->open();

    try {
        // Insert the reply into the comments table
        $stmt = $conn->prepare("INSERT INTO comment (product_id, user_id, comment, created_at, parent_id) 
                                VALUES (:product_id, :user_id, :comment, :created_at, :parent_id)");
        $now = date('Y-m-d H:i:s');
        $stmt->execute([
            'product_id' => getProductIDByComment($conn, $parent_id), // Helper function to get product ID from comment
            'user_id' => $user_id,
            'comment' => $reply,
            'created_at' => $now,
            'parent_id' => $parent_id // Set the parent ID of the original comment
        ]);

        // Return success response
        echo json_encode([
            'success' => true,
            'message' => 'Reply submitted successfully.'
        ]);
    } catch (PDOException $e) {
        // Return error response
        echo json_encode([
            'success' => false,
            'message' => 'Failed to submit reply. Error: ' . $e->getMessage()
        ]);
    }

    // Close connection
    $pdo->close();
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid input. Please provide a valid comment reply.'
    ]);
}

// Helper function to get product ID from the parent comment
function getProductIDByComment($conn, $parent_id) {
    $stmt = $conn->prepare("SELECT product_id FROM comment WHERE id = :id");
    $stmt->execute(['id' => $parent_id]);
    $row = $stmt->fetch();
    return $row['product_id'];
}
?>
