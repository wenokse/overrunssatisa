<?php
include 'includes/session.php';
$conn = $pdo->open();

if(isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if($action == 'send') {
        $sender_id = $_SESSION['user'];
        $receiver_id = $_POST['receiver_id'];
        $message = $_POST['message'];
        
        // Corrected query by moving NOW() out of the placeholder
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message, timestamp) VALUES (:sender_id, :receiver_id, :message, NOW())");
        $stmt->execute(['sender_id' => $sender_id, 'receiver_id' => $receiver_id, 'message' => $message]);
        
        echo json_encode(['status' => 'success']);
    }
    if ($_POST['action'] == 'edit') {
        $message_id = $_POST['message_id'];
        $new_message = $_POST['new_message'];
    
        $stmt = $conn->prepare("UPDATE messages SET message = :message WHERE id = :message_id");
        $stmt->execute(['message' => $new_message, 'message_id' => $message_id]);
    
        echo json_encode(['status' => 'success']);
        exit();
    }
    
    if ($_POST['action'] == 'delete') {
        $message_id = $_POST['message_id'];
    
        // Mark message as unsent
        $stmt = $conn->prepare("UPDATE messages SET message = 'Message unsent' WHERE id = :message_id");
        $stmt->execute(['message_id' => $message_id]);
    
        echo json_encode(['status' => 'success']);
        exit();
    }
    
    elseif($action == 'get') {
        $user_id = $_SESSION['user'];
        $other_id = $_POST['other_id'];
        $last_id = isset($_POST['last_id']) ? intval($_POST['last_id']) : 0;
        
        $stmt = $conn->prepare("SELECT m.*, u.firstname, u.lastname, u.photo FROM messages m 
                                LEFT JOIN users u ON u.id = m.sender_id
                                WHERE ((m.sender_id = :user_id AND m.receiver_id = :other_id)
                                OR (m.sender_id = :other_id AND m.receiver_id = :user_id))
                                AND m.id > :last_id
                                ORDER BY m.timestamp ASC");
        $stmt->execute(['user_id' => $user_id, 'other_id' => $other_id, 'last_id' => $last_id]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode($messages);
    }
}

$pdo->close();
?>
