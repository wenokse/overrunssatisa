<?php
include 'includes/session.php';

header('Content-Type: application/json');
$response = ['error' => false];

if (isset($_SESSION['phone'])) {
    $phone = $_SESSION['phone'];

    $conn = $pdo->open();

    try {
        // Generate new OTP
        $otp = sprintf("%06d", mt_rand(0, 999999));
        $expiry = time() + 600; // OTP valid for 10 minutes

        // Update OTP and expiry in the database
        $stmt = $conn->prepare("UPDATE address_verification SET otp = :otp, expiry = :expiry WHERE phone = :phone");
        $stmt->execute(['otp' => $otp, 'expiry' => $expiry, 'phone' => $phone]);

        // Resend OTP via Infobip
        $international_format = '+63' . substr($phone, 1);
        $base_url = 'https://69y84d.api.infobip.com';
        $api_key = 'f8e95ad451e731b7d04c6c087427a1a5-bbc9cd9a-f53a-4c3c-be91-9ce46618dd72';
        $payload = [
            'messages' => [
                [
                    'from' => 'OverrunsSaTisa',
                    'destinations' => [['to' => $international_format]],
                    'text' => "Your new OTP is: $otp. It is valid for 10 minutes.",
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

        $response['message'] = 'A new OTP has been sent to your phone.';
    } catch (PDOException $e) {
        $response['error'] = true;
        $response['message'] = $e->getMessage();
    }

    $pdo->close();
} else {
    $response['error'] = true;
    $response['message'] = 'Phone number not found in session.';
}

echo json_encode($response);
?>
