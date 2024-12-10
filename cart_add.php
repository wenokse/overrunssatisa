<!-- cart add -->
<?php
include 'includes/session.php';

$conn = $pdo->open();

$output = array('error' => false);

$id = $_POST['id'];
$quantity = $_POST['quantity'];
$size = $_POST['size']; 
$color = $_POST['selected_color'];
$shipping = $_POST['shipping']; 
$vendor_id = $_POST['vendor_id']; // Get vendor_id from POST data

if(isset($_SESSION['user'])){
    try {
        // Fetch product details
        $stmt_product = $conn->prepare("SELECT stock FROM products WHERE id=:product_id");
        $stmt_product->execute(['product_id' => $id]);
        $row_product = $stmt_product->fetch();

        if ($row_product) {
            $available_stock = $row_product['stock'];

            if ($quantity > $available_stock) {
                $output['error'] = true;
                $output['message'] = 'Not enough stock available';
                echo json_encode($output);
                exit();
            }

            $stmt_cart = $conn->prepare("SELECT COUNT(*) AS numrows, shipping FROM cart WHERE user_id=:user_id AND product_id=:product_id"); 
            $stmt_cart->execute(['user_id' => $user['id'], 'product_id' => $id]); 
            $row_cart = $stmt_cart->fetch();

            if($row_cart['numrows'] < 1){
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
            $stmt = $conn->prepare("INSERT INTO cart (user_id, admin_id, product_id, size, color, quantity, shipping) VALUES (:user_id, :admin_id, :product_id, :size, :color, :quantity, :shipping)");
            $stmt->execute([
                'user_id' => $user['id'], 
                'admin_id' => $vendor_id, // Use vendor_id from POST data
                'product_id' => $id, 
                'size' => $size, 
                'color' => $color, 
                'quantity' => $quantity, 
                'shipping' => $shipping
            ]);
            $new_cart_id = $conn->lastInsertId();
            $output['cart_id'] = $new_cart_id;
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
    $output['redirect'] = 'login'; 
}

$pdo->close();
echo json_encode($output);
?>