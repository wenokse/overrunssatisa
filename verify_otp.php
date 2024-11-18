<?php
include 'includes/session.php';

header('Content-Type: application/json');
$response = ['error' => false];

if (isset($_POST['otp'])) {
    $otp = $_POST['otp'];
    $phone = $_SESSION['phone'] ?? null;

    if (!$phone) {
        $response['error'] = true;
        $response['message'] = 'Phone number not found in session.';
        echo json_encode($response);
        exit();
    }

    $conn = $pdo->open();

    try {
        // Check the OTP and expiry
        $stmt = $conn->prepare("SELECT * FROM address_verification WHERE phone = :phone AND otp = :otp AND expiry > :current_time");
        $stmt->execute(['phone' => $phone, 'otp' => $otp, 'current_time' => time()]);
        $result = $stmt->fetch();

        if ($result) {
            // Move address to delivery_address table
            $stmt = $conn->prepare("INSERT INTO delivery_address (user_id, recipient_name, phone, address, address2, address3) 
                                    VALUES (:user_id, :recipient_name, :phone, :address, :address2, :address3)");
            $stmt->execute([
                'user_id' => $result['user_id'],
                'recipient_name' => $result['recipient_name'],
                'phone' => $result['phone'],
                'address' => $result['address'],
                'address2' => $result['address2'],
                'address3' => $result['address3']
            ]);

            // Delete from address_verification table
            $stmt = $conn->prepare("DELETE FROM address_verification WHERE phone = :phone");
            $stmt->execute(['phone' => $phone]);

            $response['message'] = 'Address verified and saved successfully.';
        } else {
            $response['error'] = true;
            $response['message'] = 'Invalid or expired OTP.';
        }
    } catch (PDOException $e) {
        $response['error'] = true;
        $response['message'] = $e->getMessage();
    }

    $pdo->close();
} else {
    $response['error'] = true;
    $response['message'] = 'OTP is required.';
}

echo json_encode($response);
?>
