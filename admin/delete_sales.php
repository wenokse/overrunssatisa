<?php
include 'includes/session.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['user_id'])) {
        $user_id = $_POST['user_id'];
        
        try {
            $conn = $pdo->open();

            $stmt = $conn->prepare("DELETE FROM sales WHERE user_id = :user_id");
            $stmt->execute(['user_id' => $user_id]);

            $_SESSION['success'] = 'Transaction deleted successfully';
        } catch (PDOException $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        $pdo->close();
    } else {
        $_SESSION['error'] = 'Transaction ID not provided';
    }
} else {
    $_SESSION['error'] = 'Invalid request';
}

header('location: transactions.php'); // Replace 'transactions.php' with the appropriate page
exit();
?>
