<?php
include 'includes/session.php';

$conn = $pdo->open();

$output = array('error' => false);

$id = $_POST['id'];
$quantity = $_POST['quantity'];
$size = $_POST['size']; 
$color = $_POST['color'];
$shipping = $_POST['shipping']; 

if(isset($_SESSION['user'])){
    try {
        // Check available stock for the product
        $stmt_stock = $conn->prepare("SELECT stock FROM products WHERE id=:product_id");
        $stmt_stock->execute(['product_id' => $id]);
        $row_stock = $stmt_stock->fetch();

        if ($row_stock) {
            $available_stock = $row_stock['stock'];

            if ($quantity > $available_stock) {
                $output['error'] = true;
                $output['message'] = 'Not enough stock available';
                echo json_encode($output);
                exit();
            }

            $stmt_product = $conn->prepare("SELECT COUNT(*) AS numrows, shipping FROM cart WHERE user_id=:user_id AND product_id=:product_id"); 
            $stmt_product->execute(['user_id' => $user['id'], 'product_id' => $id]); 
            $row_product = $stmt_product->fetch();

            if($row_product['numrows'] < 1){
                $shipping = 100;
            }
            else{
                $stmt_check = $conn->prepare("SELECT * FROM cart WHERE user_id=:user_id AND product_id=:product_id AND size=:size AND color=:color"); 
                $stmt_check->execute(['user_id' => $user['id'], 'product_id' => $id, 'size' => $size, 'color' => $color]); 
                $check_row = $stmt_check->fetch();

                if($check_row) {
                    // Update quantity if the same product, size, and color combination is found
                    $new_quantity = $check_row['quantity'] + $quantity;

                    if ($new_quantity > $available_stock) {
                        $output['error'] = true;
                        $output['message'] = 'Not enough stock available';
                        echo json_encode($output);
                        exit();
                    }

                    $stmt_update = $conn->prepare("UPDATE cart SET quantity=:quantity WHERE id=:cart_id");
                    $stmt_update->execute(['quantity' => $new_quantity, 'cart_id' => $check_row['id']]);
                    $output['message'] = 'Item added to cart';
                    echo json_encode($output);
                    exit();
                } else {
                    $shipping = 0;
                }
            }

            // Insert new item into cart
            $stmt = $conn->prepare("INSERT INTO cart (user_id, size, color, product_id, quantity, shipping) VALUES (:user_id, :size, :color, :product_id, :quantity, :shipping)");
            $stmt->execute(['user_id' => $user['id'], 'size' => $size, 'color' => $color, 'product_id' => $id, 'quantity' => $quantity, 'shipping' => $shipping]);
            $output['message'] = 'Item added to cart';
        } else {
            $output['error'] = true;
            $output['message'] = 'Product not found';
        }
    } catch (PDOException $e) {
        $output['error'] = true;
        $output['message'] = $e->getMessage();
    }
} else {
    // Redirect user to login page if not logged in
    $output['error'] = true;
    $output['message'] = 'Login first';
    $output['redirect'] = 'login.php'; 
}

$pdo->close();
echo json_encode($output);
?>
