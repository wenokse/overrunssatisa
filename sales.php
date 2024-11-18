<?php
include 'includes/session.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $date = date('Y-m-d');
    $conn = $pdo->open();

    try {
        $conn->beginTransaction();

        $stmt_address = $conn->prepare("SELECT * FROM delivery_address WHERE user_id = :user_id");
        $stmt_address->execute(['user_id' => $user['id']]);
        $delivery_address = $stmt_address->fetch();

        if ($delivery_address) {
            $stmt_insert_home = $conn->prepare("INSERT INTO home_address (user_id, sales_id, recipient_name, phone, address, address2, address3, created_at) 
                VALUES (:user_id, :sales_id, :recipient_name, :phone, :address, :address2, :address3, NOW())");

            $address_params = [
                'user_id' => $user['id'],
                'sales_id' => null,
                'recipient_name' => $delivery_address['recipient_name'],
                'phone' => $delivery_address['phone'],
                'address' => $delivery_address['address'],
                'address2' => $delivery_address['address2'],
                'address3' => $delivery_address['address3']
            ];
        }

        $selected_products = isset($_POST['selected_products']) ? explode(',', $_POST['selected_products']) : [];

        if (!empty($selected_products)) {
            // Get unique admin_ids (shops) from selected products
            $stmt_get_admin_ids = $conn->prepare("SELECT DISTINCT c.admin_id FROM cart c WHERE c.id IN (" . implode(',', array_map('intval', $selected_products)) . ")");
            $stmt_get_admin_ids->execute();
            $admin_ids = $stmt_get_admin_ids->fetchAll(PDO::FETCH_COLUMN);

            foreach ($admin_ids as $admin_id) {
                $pay_id = uniqid('');

                // Calculate total amount for products from this shop (excluding shipping)
                $stmt_calc_amount = $conn->prepare("
                    SELECT SUM(c.quantity * p.price) as subtotal,
                           COUNT(DISTINCT c.id) as item_count
                    FROM cart c 
                    JOIN products p ON p.id = c.product_id 
                    WHERE c.admin_id = :admin_id 
                    AND c.id IN (" . implode(',', array_map('intval', $selected_products)) . ")
                ");
                $stmt_calc_amount->execute(['admin_id' => $admin_id]);
                $amount_result = $stmt_calc_amount->fetch();
                
                // Add flat rate shipping of 100 per shop
                $shipping = 100;
                $vendor_amount = $amount_result['subtotal'] + $shipping;

                // Insert into sales table
                $stmt_insert_sales = $conn->prepare("
                    INSERT INTO sales (user_id, admin_id, pay_id, sales_date, vendor_amount, status) 
                    VALUES (:user_id, :admin_id, :pay_id, :sales_date, :vendor_amount, 0)
                ");
                $stmt_insert_sales->execute([
                    'user_id' => $user['id'],
                    'admin_id' => $admin_id,
                    'pay_id' => $pay_id,
                    'sales_date' => $date,
                    'vendor_amount' => $vendor_amount
                ]);
                
                $salesid = $conn->lastInsertId();

                if ($delivery_address) {
                    $address_params['sales_id'] = $salesid;
                    $stmt_insert_home->execute($address_params);
                }

                // Get cart items for this shop
                $stmt_select_cart = $conn->prepare("
                    SELECT c.*, p.price 
                    FROM cart c 
                    JOIN products p ON p.id = c.product_id 
                    WHERE c.user_id = :user_id 
                    AND c.admin_id = :admin_id 
                    AND c.id IN (" . implode(',', array_map('intval', $selected_products)) . ")
                ");
                $stmt_select_cart->execute(['user_id' => $user['id'], 'admin_id' => $admin_id]);
                $cart_items = $stmt_select_cart->fetchAll();

                // Calculate shipping per item (divide total shipping by number of items)
                $shipping_per_item = $shipping / count($cart_items);

                foreach ($cart_items as $row) {
                    $stmt_insert_details = $conn->prepare("
                        INSERT INTO details (sales_id, size, color, product_id, quantity, shipping) 
                        VALUES (:sales_id, :size, :color, :product_id, :quantity, :shipping)
                    ");
                    $stmt_insert_details->execute([
                        'sales_id' => $salesid,
                        'size' => $row['size'],
                        'color' => $row['color'],
                        'product_id' => $row['product_id'],
                        'quantity' => $row['quantity'],
                        'shipping' => $shipping_per_item // Distribute shipping cost evenly among items
                    ]);

                    $stmt_update_stock = $conn->prepare("UPDATE products SET stock = stock - :quantity WHERE id = :product_id");
                    $stmt_update_stock->execute(['quantity' => $row['quantity'], 'product_id' => $row['product_id']]);
                }

                // Clear processed items from cart
                $stmt_clear_cart = $conn->prepare("DELETE FROM cart WHERE user_id = :user_id AND admin_id = :admin_id AND id IN (" . implode(',', array_map('intval', $selected_products)) . ")");
                $stmt_clear_cart->execute(['user_id' => $user['id'], 'admin_id' => $admin_id]);
            }

            $conn->commit();
            $_SESSION['success'] = 'Transaction successful. Thank you!';
        } else {
            $_SESSION['error'] = 'No items selected for checkout.';
        }
    } catch (PDOException $e) {
        $conn->rollBack();
        $_SESSION['error'] = 'Error: ' . $e->getMessage();
    }

    $pdo->close();

    header('location: profile');
    exit();
} else {
    $_SESSION['error'] = 'Invalid request method.';
    header('location: profile');
    exit();
}
?>