<?php
include 'includes/session.php';

if(isset($_GET['category_id'])){
    $category_id = $_GET['category_id'];
    
    $conn = $pdo->open();

    try{
        $stmt = $conn->prepare("SELECT * FROM products WHERE category_id=:category_id");
        $stmt->execute(['category_id'=>$category_id]);
        $products = $stmt->fetchAll();
        echo json_encode($products);
    }
    catch(PDOException $e){
        echo json_encode(['error' => $e->getMessage()]);
    }

    $pdo->close();
}
?>
