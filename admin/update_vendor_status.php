<?php
include 'includes/session.php';

if (isset($_POST['id']) && isset($_POST['status'])) {
    $id = $_POST['id'];
    $status = $_POST['status'];

    $conn = $pdo->open();

    try {
        $stmt = $conn->prepare("UPDATE users SET status=:status WHERE id=:id");
        $stmt->execute(['status' => $status, 'id' => $id]);
        $_SESSION['success'] = 'Vendor status updated successfully';
    } catch (PDOException $e) {
        $_SESSION['error'] = $e->getMessage();
    }

    $pdo->close();
} else {
    $_SESSION['error'] = 'Invalid request';
}

header('location: vendor.php');
?>
