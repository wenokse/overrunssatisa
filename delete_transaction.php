<?php
include 'includes/session.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['id'])) {
        $id = $_POST['id'];
        $response = array(); 
        
        try {
            $conn = $pdo->open();

           
            $stmt = $conn->prepare("SELECT product_id, quantity FROM details WHERE sales_id = :id");
            $stmt->execute(['id' => $id]);
            $details_rows = $stmt->fetchAll();

           
            $stmt = $conn->prepare("DELETE FROM sales WHERE id = :id");
            $stmt->execute(['id' => $id]);

           
            foreach ($details_rows as $details_row) {
                $stmt = $conn->prepare("UPDATE products SET stock = stock + :quantity WHERE id = :product_id");
                $stmt->execute(['quantity' => $details_row['quantity'], 'product_id' => $details_row['product_id']]);
            }

            $response['success'] = true;
            
        } catch (PDOException $e) {
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }

        $pdo->close();
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    } else {
        $response['success'] = false;
        $response['message'] = 'Transaction ID not provided';
    }
} else {
    $response['success'] = false;
    $response['message'] = 'Invalid request';
}


header('Content-Type: application/json');
echo json_encode($response);
exit();
?>
