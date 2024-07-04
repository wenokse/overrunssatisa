<?php
include 'includes/session.php';

$conn = $pdo->open();

$output = array('error' => false);
$id = $_POST['id'];

if (isset($_SESSION['user'])) {
    try {
        // Fetch the row to be deleted
        $stmt = $conn->prepare("SELECT * FROM cart WHERE id=:id AND user_id=:user_id");
        $stmt->execute(['id' => $id, 'user_id' => $_SESSION['user']]);
        $row = $stmt->fetch();

        if ($row) {
            $product_id = $row['product_id'];
            $shipping = $row['shipping'];

            // Delete the row
            $stmt = $conn->prepare("DELETE FROM cart WHERE id=:id");
            $stmt->execute(['id' => $id]);

            if ($shipping == 100) {
                // Transfer the shipping cost to another product with the same product_id and 0 shipping
                $stmt_transfer = $conn->prepare("SELECT * FROM cart WHERE user_id=:user_id AND product_id=:product_id AND shipping=0 ORDER BY id LIMIT 1");
                $stmt_transfer->execute(['user_id' => $_SESSION['user'], 'product_id' => $product_id]);
                $row_transfer = $stmt_transfer->fetch();

                if ($row_transfer) {
                    $stmt_update_shipping = $conn->prepare("UPDATE cart SET shipping=100 WHERE id=:id");
                    $stmt_update_shipping->execute(['id' => $row_transfer['id']]);
                }
            }

            $output['message'] = 'Deleted';
        } else {
            $output['error'] = true;
            $output['message'] = 'Product not found in cart';
        }
    } catch (PDOException $e) {
        $output['error'] = true;
        $output['message'] = $e->getMessage();
    }
} else {
    foreach ($_SESSION['cart'] as $key => $row) {
        if ($row['productid'] == $id) {
            $shipping = $row['shipping'];
            unset($_SESSION['cart'][$key]);
            $output['message'] = 'Deleted';

            if ($shipping == 100) {
                foreach ($_SESSION['cart'] as $innerKey => $innerRow) {
                    if ($innerRow['productid'] == $id && $innerRow['shipping'] == 0) {
                        $_SESSION['cart'][$innerKey]['shipping'] = 100;
                        break;
                    }
                }
            }
            break;
        }
    }
}

$pdo->close();
echo json_encode($output);
?>
