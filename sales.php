<?php
include 'includes/session.php';

if(isset($_GET['pay'])){
    $payid = $_GET['pay'];
    $date = date('Y-m-d');
    $conn = $pdo->open();

    try {
        
        
       
        $stmt_insert_sales = $conn->prepare("INSERT INTO sales (user_id, pay_id, sales_date) VALUES (:user_id, :pay_id, :sales_date)");
        $stmt_insert_sales->execute(['user_id' => $user['id'], 'pay_id' => $payid, 'sales_date' => $date]);
        $salesid = $conn->lastInsertId();

       
        $stmt_select_cart = $conn->prepare("SELECT * FROM cart WHERE user_id=:user_id");
        $stmt_select_cart->execute(['user_id'=>$user['id']]);

        foreach($stmt_select_cart as $row) {
            $size = $row['size'];
            $color = $row['color'];
            $product_id = $row['product_id'];
            $quantity = $row['quantity'];
            $shipping = $row['shipping'];
            

            $stmt_insert_details = $conn->prepare("INSERT INTO details (sales_id, size, color, product_id, quantity, shipping) VALUES (:sales_id, :size, :color, :product_id, :quantity, :shipping)");
            $stmt_insert_details->execute(['sales_id'=>$salesid, 'size'=>$size, 'color'=>$color, 'product_id'=>$product_id, 'quantity'=>$quantity, 'shipping'=>$shipping]);

            
            $stmt_update_stock = $conn->prepare("UPDATE products SET stock = stock - :quantity WHERE id = :product_id");
            $stmt_update_stock->execute(['quantity'=>$quantity, 'product_id'=>$product_id]);
        }

        
        $stmt_clear_cart = $conn->prepare("DELETE FROM cart WHERE user_id=:user_id");
        $stmt_clear_cart->execute(['user_id'=>$user['id']]);

        $_SESSION['success'] = 'Transaction successful. Thank you!';
    } catch(PDOException $e) {
        $_SESSION['error'] = 'Error: ' . $e->getMessage();
    }

    $pdo->close();

    header('location: profile.php');
    exit();
} else {
    $_SESSION['error'] = 'Invalid payment parameter.';
    header('location: profile.php');
    exit();
}

?>

