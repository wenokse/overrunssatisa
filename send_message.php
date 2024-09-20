<?php
include 'includes/session.php';
$conn = $pdo->open();

if(isset($_POST['message']) && isset($_POST['receiver_id'])){
    $message = $_POST['message'];
    $receiver_id = $_POST['receiver_id'];
    $sender_id = $_SESSION['user']['id']; // Assuming the user is logged in

    // Insert the message into the database
    $stmt = $conn->prepare("
        INSERT INTO messages (sender_id, receiver_id, message, timestamp, is_read) 
        VALUES (:sender_id, :receiver_id, :message, NOW(), 0)
    ");
    $stmt->execute([
        'sender_id' => $sender_id,
        'receiver_id' => $receiver_id,
        'message' => $message
    ]);

    echo "Message sent";
} else {
    echo "Message failed to send";
}

$pdo->close();
?>
