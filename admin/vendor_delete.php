<?php
include 'includes/session.php';

if (isset($_POST['id'])) {
    $id = $_POST['id'];

    $conn = $pdo->open();

    try {
        // Delete associated products
        $stmt = $conn->prepare("DELETE FROM products WHERE user_id = :id");
        $stmt->execute(['id' => $id]);

        // Delete associated product colors and sizes
        $stmt = $conn->prepare("DELETE FROM product_colors WHERE product_id IN (SELECT id FROM products WHERE user_id = :id)");
        $stmt->execute(['id' => $id]);

        $stmt = $conn->prepare("DELETE FROM product_sizes WHERE product_id IN (SELECT id FROM products WHERE user_id = :id)");
        $stmt->execute(['id' => $id]);

        // Finally, delete the vendor
        $stmt = $conn->prepare("DELETE FROM users WHERE id = :id AND type = 2");
        $stmt->execute(['id' => $id]);

        $_SESSION['success'] = 'Vendor and associated data deleted successfully.';
    } catch (PDOException $e) {
        $_SESSION['error'] = $e->getMessage();
    }

    $pdo->close();
} else {
    $_SESSION['error'] = 'No vendor selected for deletion.';
}

header('location: vendor');
?>