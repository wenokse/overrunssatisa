<?php
include 'includes/session.php';

// Ensure the admin login email is set in the session
if (!isset($_SESSION['admin_login_email'])) {
    $_SESSION['error'] = 'Please log in first.';
    header('Location: login');
    exit();
}

// Function to resend admin login OTP
function resendAdminLoginOTP($contact_info, $firstname) {
    // Generate a secure OTP and expiry
    $otp = generateSecureOTP();
    $expiry = time() + (10 * 60); // 10 minutes expiry

    try {
        // Store the OTP in the session
        $_SESSION['otp'] = [
            'contact_info' => $contact_info,
            'otp' => $otp,
            'expiry' => $expiry
        ];

        // Ensure the phone number is in international format
        $international_format = '+63' . substr($contact_info, 1);

        // Infobip SMS Configuration
        $base_url = 'https://69y84d.api.infobip.com';
        $api_key = 'f8e95ad451e731b7d04c6c087427a1a5-bbc9cd9a-f53a-4c3c-be91-9ce46618dd72';

        // Prepare SMS payload
        $payload = [
            'messages' => [
                [
                    'from' => 'OverrunsSaTisa',
                    'destinations' => [
                        ['to' => $international_format]
                    ],
                    'text' => "Hello {$firstname}, Your NEW Admin Login OTP is: {$otp}. This code expires in 10 minutes.",
                    'flash' => false,
                    'validityPeriod' => 600
                ]
            ]
        ];

        // Send SMS via cURL
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $base_url . '/sms/2/text/advanced',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: App ' . $api_key
            ],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_TIMEOUT => 30
        ]);

        $response = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($curl);
        curl_close($curl);

        // Check SMS sending response
        if ($response === false) {
            error_log("SMS sending failed. cURL Error: " . $curl_error);
            return false;
        }

        $response_data = json_decode($response, true);

        // Verify if the SMS was sent successfully
        return ($http_code === 200 &&
            isset($response_data['messages'][0]['status']['groupId']) &&
            $response_data['messages'][0]['status']['groupId'] === 1);

    } catch (Exception $e) {
        error_log("Error resending admin login OTP: " . $e->getMessage());
        return false;
    }
}

// Handle OTP Resend Request
if (isset($_GET['resend']) && isset($_SESSION['admin_login_email'])) {
    $email = $_SESSION['admin_login_email'];
    $contact_info = $_SESSION['admin_login_contact'];
    $firstname = $_SESSION['admin_login_firstname']; // Assuming firstname is stored in session

    // Rate limiting: prevent too frequent resends
    if (isset($_SESSION['last_otp_resend']) &&
        (time() - $_SESSION['last_otp_resend']) < 60) {
        $_SESSION['error'] = 'Please wait 60 seconds before requesting a new OTP.';
        header('Location: admin_otp_verify');
        exit();
    }

    // Attempt to resend OTP
    $resend_result = resendAdminLoginOTP($contact_info, $firstname);

    if ($resend_result) {
        // Update last resend time
        $_SESSION['last_otp_resend'] = time();
        $_SESSION['success'] = 'A new OTP has been sent to your mobile number.';
    } else {
        $_SESSION['error'] = 'Failed to resend OTP. Please try again.';
    }

    header('Location: admin_otp_verify');
    exit();
}
?>
