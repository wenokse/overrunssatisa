<?php
include 'includes/session.php';

$sale_id = $_GET['sale_id'];
$conn = $pdo->open();

try {
    // Start transaction
    $conn->beginTransaction();
    
    // Get the sale details including vendor_amount
    $stmt_sale = $conn->prepare("SELECT vendor_amount, admin_id FROM sales WHERE id = :sale_id");
    $stmt_sale->execute(['sale_id' => $sale_id]);
    $sale = $stmt_sale->fetch();
    
    if (!$sale) {
        throw new Exception('Sale not found');
    }
    
    // Get order details
    $stmt_details = $conn->prepare("SELECT * FROM details WHERE sales_id = :sale_id");
    $stmt_details->execute(['sale_id' => $sale_id]);
    $details = $stmt_details->fetchAll();
    
    $total_admin_price = 0;
    
    foreach($details as $detail) {
        // Calculate admin price (10 per quantity)
        $admin_price = 10 * $detail['quantity'];
        $total_admin_price += $admin_price;
        
        // Insert into admin_sales
        $stmt_admin_sales = $conn->prepare("
            INSERT INTO admin_sales (sales_id, size, color, product_id, quantity, admin_price) 
            VALUES (:sales_id, :size, :color, :product_id, :quantity, :admin_price)
        ");
        
        $stmt_admin_sales->execute([
            'sales_id' => $sale_id,
            'size' => $detail['size'],
            'color' => $detail['color'],
            'product_id' => $detail['product_id'],
            'quantity' => $detail['quantity'],
            'admin_price' => $admin_price
        ]);
    }
    
    // Update sale status to 4 (received) and subtract admin_price from vendor_amount
    $stmt_update_sales = $conn->prepare("
        UPDATE sales 
        SET status = 4, 
            vendor_amount = vendor_amount - :admin_price 
        WHERE id = :sale_id
    ");
    $stmt_update_sales->execute([
        'sale_id' => $sale_id,
        'admin_price' => $total_admin_price
    ]);
    
    // Commit transaction
    $conn->commit();
    
    $_SESSION['success'] = 'Order received successfully. Admin fees deducted and recorded.';
    
} catch(Exception $e) {
    // Rollback transaction on error
    $conn->rollBack();
    $_SESSION['error'] = $e->getMessage();
}

$pdo->close();

header('Location: ' . $_SERVER['HTTP_REFERER']);
?>