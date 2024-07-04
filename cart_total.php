<?php
include 'includes/session.php';

if(isset($_SESSION['user'])){
    $conn = $pdo->open();

    $stmt = $conn->prepare("SELECT cart.*, products.price, shipping FROM cart LEFT JOIN products ON products.id = cart.product_id WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $user['id']]);

    $total = 0;
    foreach($stmt as $row){
        $subtotal = $row['price'] * $row['quantity'] + $row['shipping']; 
        $total += $subtotal;
    }

    $pdo->close();

    echo json_encode($total);
}
?>
