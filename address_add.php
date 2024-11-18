<?php
include 'includes/session.php';

header('Content-Type: application/json');
$response = ['error' => false];

if (isset($_POST['recipient_name'])) {
    $recipient_name = $_POST['recipient_name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $address2 = $_POST['address2'];
    $address3 = $_POST['address3'];

    // Validate the phone number
    if (!preg_match('/^\d{11}$/', $phone)) {
        $response['error'] = true;
        $response['message'] = 'Phone number must contain exactly 11 digits.';
        echo json_encode($response);
        exit();
    }

    $conn = $pdo->open();

    try {
        // Generate OTP
        $otp = sprintf("%06d", mt_rand(0, 999999));
        $expiry = time() + 600; // OTP valid for 10 minutes

        // Insert address and OTP into address_verification table
        $stmt = $conn->prepare("INSERT INTO address_verification (user_id, recipient_name, phone, address, address2, address3, otp, expiry) 
                                VALUES (:user_id, :recipient_name, :phone, :address, :address2, :address3, :otp, :expiry)");
        $stmt->execute([
            'user_id' => $user['id'],
            'recipient_name' => $recipient_name,
            'phone' => $phone,
            'address' => $address,
            'address2' => $address2,
            'address3' => $address3,
            'otp' => $otp,
            'expiry' => $expiry
        ]);

        // Save phone to session
        $_SESSION['phone'] = $phone;

        // Send OTP via Infobip
        $international_format = '+63' . substr($phone, 1);
        $base_url = 'https://69y84d.api.infobip.com';
        $api_key = 'f8e95ad451e731b7d04c6c087427a1a5-bbc9cd9a-f53a-4c3c-be91-9ce46618dd72';
        $payload = [
            'messages' => [
                [
                    'from' => 'OverrunsSaTisa',
                    'destinations' => [['to' => $international_format]],
                    'text' => "Your OTP is: $otp. It is valid for 10 minutes.",
                ]
            ]
        ];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "$base_url/sms/2/text/advanced",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: App ' . $api_key
            ],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
        ]);
        curl_exec($curl);
        curl_close($curl);

        $response['message'] = 'OTP sent to your phone. Please verify.';
    } catch (PDOException $e) {
        $response['error'] = true;
        $response['message'] = $e->getMessage();
    }

    $pdo->close();
} else {
    $response['error'] = true;
    $response['message'] = 'Fill up address form first.';
}

echo json_encode($response);
?>
