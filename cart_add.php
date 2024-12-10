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
        // Fetch product details
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

        // Check if user is adding more than available stock
        $stmt_cart = $conn->prepare("
            SELECT COALESCE(SUM(quantity), 0) AS cart_quantity 
            FROM cart 
            WHERE user_id=:user_id AND product_id=:product_id AND size=:size AND color=:color
        ");
        $stmt_cart->execute(['user_id' => $user['id'], 'product_id' => $id, 'size' => $size, 'color' => $color]);
        $row_cart = $stmt_cart->fetch();
        $current_cart_quantity = $row_cart['cart_quantity'];

        if (($current_cart_quantity + $quantity) > $available_stock) {
            $output['error'] = true;
            $output['message'] = 'Not enough stock available';
            echo json_encode($output);
            exit();
        }

        // Check if item already exists in cart with the same size and color
        $stmt_check = $conn->prepare("
            SELECT id, quantity 
            FROM cart 
            WHERE user_id=:user_id AND product_id=:product_id AND size=:size AND color=:color
        ");
        $stmt_check->execute(['user_id' => $user['id'], 'product_id' => $id, 'size' => $size, 'color' => $color]);
        $check_row = $stmt_check->fetch();

        if ($check_row) {
            // Update quantity if the same product, size, and color combination is found
            $new_quantity = $check_row['quantity'] + $quantity;

            $stmt_update = $conn->prepare("UPDATE cart SET quantity=:quantity WHERE id=:cart_id");
            $stmt_update->execute(['quantity' => $new_quantity, 'cart_id' => $check_row['id']]);
            $output['message'] = 'Item quantity updated in cart';
        } else {
            // Insert new item into cart
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
            $output['message'] = 'Item added to cart';
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
