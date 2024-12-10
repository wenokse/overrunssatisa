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

if (isset($_SESSION['user'])) {
    try {
        // Fetch product stock
        $stmt_product = $conn->prepare("SELECT stock FROM products WHERE id=:product_id");
        $stmt_product->execute(['product_id' => $id]);
        $row_product = $stmt_product->fetch();

        if (!$row_product) {
            $output['error'] = true;
            $output['message'] = 'Product not found';
            echo json_encode($output);
            exit();
        }

        $available_stock = $row_product['stock'];

        // Calculate total quantity already in cart for this product
        $stmt_cart_total = $conn->prepare("
            SELECT COALESCE(SUM(quantity), 0) AS total_cart_quantity 
            FROM cart 
            WHERE user_id=:user_id AND product_id=:product_id
        ");
        $stmt_cart_total->execute(['user_id' => $user['id'], 'product_id' => $id]);
        $cart_total_row = $stmt_cart_total->fetch();
        $total_cart_quantity = $cart_total_row['total_cart_quantity'];

        // Check if adding this quantity exceeds available stock
        if (($total_cart_quantity + $quantity) > $available_stock) {
            $output['error'] = true;
            $output['message'] = 'You cannot add more than the available stock.';
            echo json_encode($output);
            exit();
        }

        // Check if the specific combination of size and color exists in the cart
        $stmt_check = $conn->prepare("
            SELECT id, quantity 
            FROM cart 
            WHERE user_id=:user_id AND product_id=:product_id AND size=:size AND color=:color
        ");
        $stmt_check->execute(['user_id' => $user['id'], 'product_id' => $id, 'size' => $size, 'color' => $color]);
        $check_row = $stmt_check->fetch();

        if ($check_row) {
            // Update quantity if the same combination exists
            $new_quantity = $check_row['quantity'] + $quantity;

            $stmt_update = $conn->prepare("UPDATE cart SET quantity=:quantity WHERE id=:cart_id");
            $stmt_update->execute(['quantity' => $new_quantity, 'cart_id' => $check_row['id']]);
            $output['message'] = 'Cart updated successfully.';
        } else {
            // Insert a new entry for the specific combination
            $stmt_insert = $conn->prepare("
                INSERT INTO cart (user_id, admin_id, product_id, size, color, quantity, shipping) 
                VALUES (:user_id, :admin_id, :product_id, :size, :color, :quantity, :shipping)
            ");
            $stmt_insert->execute([
                'user_id' => $user['id'], 
                'admin_id' => $vendor_id, 
                'product_id' => $id, 
                'size' => $size, 
                'color' => $color, 
                'quantity' => $quantity, 
                'shipping' => $shipping
            ]);
            $output['message'] = 'Item added to cart.';
        }
    } catch (PDOException $e) {
        $output['error'] = true;
        $output['message'] = 'Database error: ' . $e->getMessage();
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
