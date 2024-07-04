<?php
include 'includes/session.php';

try {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['id'])) {
            $sales_id = $_POST['id'];
            $response = array();

            $conn = $pdo->open();

            // Begin transaction
            $conn->beginTransaction();

            // Fetch necessary data from the sales table
            $stmt = $conn->prepare("SELECT * FROM sales WHERE id = :id");
            $stmt->execute(['id' => $sales_id]);
            $sales_row = $stmt->fetch();

            if (!$sales_row) {
                throw new Exception('Invalid sales ID');
            }

            // Select details associated with the sales_id
            $stmt = $conn->prepare("SELECT * FROM details WHERE sales_id = :sales_id");
            $stmt->execute(['sales_id' => $sales_id]);
            $details_rows = $stmt->fetchAll(); 

            // Calculate total quantity for each product
            $total_quantity = array();
            foreach ($details_rows as $details_row) {
                $product_id = $details_row['product_id'];
                if (!isset($total_quantity[$product_id])) {
                    $total_quantity[$product_id] = 0;
                }
                $total_quantity[$product_id] += $details_row['quantity'];
            }

            // Update product stock
            foreach ($total_quantity as $product_id => $quantity) {
                $stmt = $conn->prepare("UPDATE products SET stock = stock + :quantity WHERE id = :product_id");
                $stmt->execute(['quantity' => $quantity, 'product_id' => $product_id]);
            }

            // Insert into return_details table for each detail row
            foreach ($details_rows as $details_row) {
                $stmt = $conn->prepare("INSERT INTO return_details (sales_id, size, color, product_id, quantity, shipping) 
                                        VALUES (:sales_id, :size, :color, :product_id, :quantity, :shipping)");
                $stmt->execute([
                    'sales_id' => $sales_id,
                    'size' => $details_row['size'],
                    'color' => $details_row['color'],
                    'product_id' => $details_row['product_id'],
                    'quantity' => $details_row['quantity'],
                    'shipping' => $details_row['shipping']
                ]);
            }

            // Insert into return_products table
            $stmt = $conn->prepare("INSERT INTO return_products (pay_id, user_id, shipping, return_date, sales_id) 
                                    VALUES (:pay_id, :user_id, :shipping, :return_date, :sales_id)");
            $stmt->execute([
                'pay_id' => $sales_row['pay_id'],
                'user_id' => $sales_row['user_id'],
                'shipping' => end($details_rows)['shipping'], // Use the last details row for shipping info
                'return_date' => date('Y-m-d'),
                'sales_id' => $sales_id
            ]);

            // Delete from sales table
            $stmt = $conn->prepare("DELETE FROM sales WHERE id = :id");
            $stmt->execute(['id' => $sales_id]);

            // Commit transaction
            $conn->commit();

           
            $response['success'] = true;
           
        } else {
            throw new Exception('Sales ID not provided');
        }
    } else {
        throw new Exception('Invalid request');
    }
} catch (PDOException $e) {
    // Rollback transaction on database error
    $conn->rollBack();

    $_SESSION['error'] = $e->getMessage();
    $response['success'] = false;
    $response['message'] = $e->getMessage();
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    $response['success'] = false;
    $response['message'] = $e->getMessage();
} finally {
    $pdo->close();
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// Redirect user after processing
header('Location: transactions.php');
exit();
