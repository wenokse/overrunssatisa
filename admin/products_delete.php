<?php
    include 'includes/session.php';

    if(isset($_POST['delete'])){
        $id = $_POST['id'];
        
        $conn = $pdo->open();

        try{
            $conn->beginTransaction();
            $stmt = $conn->prepare("DELETE FROM product_colors WHERE product_id=:id");
            $stmt->execute(['id'=>$id]);
            $stmt = $conn->prepare("DELETE FROM product_sizes WHERE product_id=:id");
            $stmt->execute(['id'=>$id]);
            $stmt = $conn->prepare("DELETE FROM products WHERE id=:id");
            $stmt->execute(['id'=>$id]);
            $conn->commit();

            $_SESSION['success'] = 'Product deleted successfully';
        }
        catch(PDOException $e){
            // Rollback the transaction in case of error
            $conn->rollBack();
            $_SESSION['error'] = $e->getMessage();
        }

        $pdo->close();
    }
    else{
        $_SESSION['error'] = 'Select product to delete first';
    }

    header('location: products');
?>
