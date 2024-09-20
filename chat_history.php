<?php
// chat_history.php
include 'includes/session.php';
$conn = $pdo->open();

$sender_id = $_SESSION['user']['id'];
$receiver_id = $_GET['receiver_id'];

$stmt = $conn->prepare("
    SELECT * FROM messages 
    WHERE (sender_id = :sender_id AND receiver_id = :receiver_id) 
    OR (sender_id = :receiver_id AND receiver_id = :sender_id)
    ORDER BY timestamp ASC
");
$stmt->execute(['sender_id' => $sender_id, 'receiver_id' => $receiver_id]);
$messages = $stmt->fetchAll();

foreach ($messages as $message) {
    $message_class = ($message['sender_id'] == $sender_id) ? 'outgoing-message' : 'incoming-message';
    echo '<div class="' . $message_class . '">' . htmlspecialchars($message['message']) . '</div>';
}

$pdo->close();
?>
