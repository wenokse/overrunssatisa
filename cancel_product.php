<?php
include 'includes/session.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['detail_id'])) {
        $detail_id = $_POST['detail_id'];  // The ID of the detail being canceled
        $response = array();  // Response array to send back to the frontend
        
        try {
            $conn = $pdo->open();

            // Fetch the quantity and product_id of the detail being canceled
            $stmt = $conn->prepare("SELECT quantity, product_id FROM details WHERE id = :detail_id");
            $stmt->execute(['detail_id' => $detail_id]);
            $row = $stmt->fetch();

            // If the detail exists
            if ($row) {
                $quantity = $row['quantity'];
                $product_id = $row['product_id'];

                // Update the product stock by adding the canceled quantity back to the stock
                $stmt = $conn->prepare("UPDATE products SET stock = stock + :quantity WHERE id = :product_id");
                $stmt->execute(['quantity' => $quantity, 'product_id' => $product_id]);

                // Delete the specific detail (cancels the product in the transaction)
                $stmt = $conn->prepare("DELETE FROM details WHERE id = :detail_id");
                $stmt->execute(['detail_id' => $detail_id]);

                $response['success'] = true;  // Indicate success
            } else {
                $response['success'] = false;
                $response['message'] = 'Detail not found in the transaction.';
            }
        } catch (PDOException $e) {
            // In case of an error, return the error message
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }

        $pdo->close();
        
        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    } else {
        $response['success'] = false;
        $response['message'] = 'Detail ID not provided';
    }
} else {
    $response['success'] = false;
    $response['message'] = 'Invalid request';
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
exit();
?>