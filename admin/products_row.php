<?php
include 'includes/session.php';

if(isset($_POST['id'])){
    $id = $_POST['id'];
    
    $conn = $pdo->open();

    $stmt = $conn->prepare("SELECT *, products.id AS prodid, products.name AS prodname, category.name AS catname FROM products LEFT JOIN category ON category.id=products.category_id WHERE products.id=:id");
    $stmt->execute(['id'=>$id]);
    $row = $stmt->fetch();
    
    // Fetch colors for this product
    $stmt = $conn->prepare("SELECT * FROM product_colors WHERE product_id=:id");
    $stmt->execute(['id'=>$id]);
    $colors = $stmt->fetchAll();
    
    $stmt = $conn->prepare("SELECT * FROM product_sizes WHERE product_id=:id");
    $stmt->execute(['id'=>$id]);
    $sizes = $stmt->fetchAll();


    $row['colors'] = $colors;
    $row['sizes'] = $sizes;
    
    $pdo->close();

    echo json_encode($row);
}
?>