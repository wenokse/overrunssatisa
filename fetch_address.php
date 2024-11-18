<?php
include 'includes/session.php';

$conn = $pdo->open();
try {
    $stmt = $conn->prepare("SELECT * FROM delivery_address WHERE user_id=:user_id");
    $stmt->execute(['user_id' => $user['id']]);
    $address = $stmt->fetch();

    if($address){
        echo "
            <div class='delivery-address-display'>
                <p><strong>Name:</strong> ".htmlspecialchars($address['recipient_name'])."</p>
                <p><strong>Phone:</strong> ".htmlspecialchars($address['phone'])."</p>
                <p><strong>Address:</strong> ".htmlspecialchars($address['address'])."</p>
                <p><strong>Purok:</strong> ".htmlspecialchars($address['address2'])."</p>
                <p><strong>Address2:</strong> ".htmlspecialchars($address['address3'])."</p>
                <button type='button' class='btn btn-primary btn-sm edit-address'>Edit Address</button>
                <button type='button' class='btn btn-danger btn-sm delete-address'>Delete Address</button>
            </div>
        ";
    } else {
        echo "
            <div class='no-address-message'>
                <p>No delivery address found. Please add your delivery address.</p>
                <button type='button' class='btn btn-primary btn-sm' data-toggle='modal' data-target='#addAddressModal'>
                    Add Delivery Address
                </button>
            </div>
        ";
    }
} catch(PDOException $e) {
    echo "Connection error: " . $e->getMessage();
}
$pdo->close();
?>