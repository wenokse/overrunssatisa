<?php
include 'includes/session.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $date = date('Y-m-d');
    $conn = $pdo->open();

    try {
        // Start a transaction
        $conn->beginTransaction();

        // Get all unique admin_ids from the cart for this user
        $stmt_get_admin_ids = $conn->prepare("SELECT DISTINCT admin_id FROM cart WHERE user_id = :user_id");
        $stmt_get_admin_ids->execute(['user_id' => $user['id']]);
        $admin_ids = $stmt_get_admin_ids->fetchAll(PDO::FETCH_COLUMN);

        foreach ($admin_ids as $admin_id) {
            // Generate a unique pay_id
            $pay_id = uniqid('');

            // Insert a new sale for each admin_id
            $stmt_insert_sales = $conn->prepare("INSERT INTO sales (user_id, admin_id, pay_id, sales_date) VALUES (:user_id, :admin_id, :pay_id, :sales_date)");
            $stmt_insert_sales->execute(['user_id' => $user['id'], 'admin_id' => $admin_id, 'pay_id' => $pay_id, 'sales_date' => $date]);
            $salesid = $conn->lastInsertId();

            // Get cart items for this user and admin
            $stmt_select_cart = $conn->prepare("SELECT * FROM cart WHERE user_id = :user_id AND admin_id = :admin_id");
            $stmt_select_cart->execute(['user_id' => $user['id'], 'admin_id' => $admin_id]);

            foreach($stmt_select_cart as $row) {
                $size = $row['size'];
                $color = $row['color'];
                $product_id = $row['product_id'];
                $quantity = $row['quantity'];
                $shipping = $row['shipping'];

                $stmt_insert_details = $conn->prepare("INSERT INTO details (sales_id, size, color, product_id, quantity, shipping) VALUES (:sales_id, :size, :color, :product_id, :quantity, :shipping)");
                $stmt_insert_details->execute(['sales_id'=>$salesid, 'size'=>$size, 'color'=>$color, 'product_id'=>$product_id, 'quantity'=>$quantity, 'shipping'=>$shipping]);

                // Update stock
                $stmt_update_stock = $conn->prepare("UPDATE products SET stock = stock - :quantity WHERE id = :product_id");
                $stmt_update_stock->execute(['quantity'=>$quantity, 'product_id'=>$product_id]);
            }

            // Clear cart items for this user and admin
            $stmt_clear_cart = $conn->prepare("DELETE FROM cart WHERE user_id = :user_id AND admin_id = :admin_id");
            $stmt_clear_cart->execute(['user_id' => $user['id'], 'admin_id' => $admin_id]);
        }

        // Commit the transaction
        $conn->commit();

        $_SESSION['success'] = 'Transaction successful. Thank you!';
    } catch(PDOException $e) {
        // Rollback the transaction if there's an error
        $conn->rollBack();
        $_SESSION['error'] = 'Error: ' . $e->getMessage();
    }

    $pdo->close();

    header('location: profile.php');
    exit();
} else {
    $_SESSION['error'] = 'Invalid request method.';
    header('location: profile.php');
    exit();
}
?>