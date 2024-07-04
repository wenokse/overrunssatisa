<?php

include 'includes/session.php';
$conn = $pdo->open();


if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
    $user_id = $_SESSION['user'];
    $size = $_POST['size']; 
    $color = $_POST['color'];
    $product_id = $_POST['id']; 

   
    try {
        $query = "UPDATE cart SET size = :size, color = :color, WHERE user_id = :user_id AND product_id = :product_id";
        $stmt = $conn->prepare($query);
        $stmt->execute(['user_id' => $user_id, 'size' => $size, 'color' => $color, 'product_id' => $product_id]);

        header("Location: product.php?product=$product_id");
        exit(); 
    } catch(PDOException $e) {
        echo "There is some problem in connection: " . $e->getMessage();
    }
}


$pdo->close();
?>
