<?php
include 'includes/session.php';

$conn = $pdo->open();
try {
    // Get unrated products from completed orders (status = 4)
    $stmt = $conn->prepare("
        SELECT d.id as detail_id, d.product_id, d.sales_id, p.name 
        FROM details d 
        LEFT JOIN products p ON p.id = d.product_id 
        LEFT JOIN sales s ON s.id = d.sales_id 
        WHERE s.user_id = :user_id 
        AND s.status = 4 
        AND d.is_rated = 0 
        ORDER BY s.sales_date ASC
    ");
    
    $stmt->execute(['user_id' => $user['id']]);
    $unrated_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['unrated_products' => $unrated_products]);
}
catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

$pdo->close();
?>