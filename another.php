<?php
include 'includes/session.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate the contact number
    $contact_info = filter_var($_POST["contact_info"], FILTER_SANITIZE_STRING);

    // Validate Philippine mobile number format (09XXXXXXXXX)
    if (!preg_match("/^09\d{9}$/", $contact_info)) {
        $_SESSION['error'] = "Invalid phone number format. Please enter a valid 11-digit number starting with 09.";
        header('location: another'); // Adjust the redirect path as needed
        exit();
    }

    // Convert 09XXXXXXXXX to +63XXXXXXXXX for international SMS format
    $international_format = '+63' . substr($contact_info, 1);

    // Generate a 6-digit OTP
    $otp = sprintf("%06d", mt_rand(0, 999999));
    $expiry = time() + 600; // OTP valid for 10 minutes

    try {
        $conn = $pdo->open();

        // Check if the phone number exists in the database
        $stmt = $conn->prepare("SELECT id FROM users WHERE contact_info = :contact_info");
        $stmt->execute(['contact_info' => $contact_info]);

        if ($stmt->rowCount() == 0) {
            $_SESSION['error'] = 'Phone number not found in our records.';
            header('location: another');
            exit();
        }

        // Update the reset code and expiry in the database
        $sql = "UPDATE users 
                SET reset_code = :reset_code, reset_code_expiry = :expiry 
                WHERE contact_info = :contact_info";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'reset_code' => $otp,
            'expiry' => $expiry,
            'contact_info' => $contact_info
        ]);

        // Infobip API Configuration
        $base_url = 'https://69y84d.api.infobip.com';
        $api_key = 'f8e95ad451e731b7d04c6c087427a1a5-bbc9cd9a-f53a-4c3c-be91-9ce46618dd72';

        // Prepare the SMS payload
            $payload = [
                'messages' => [
                    [
                        'from' => 'OverrunsSaTisa', 
                        'destinations' => [
                            ['to' => $international_format]
                        ],
                        'text' => "Your OTP for password reset is: $otp. Valid for 10 minutes.",
                        'flash' => false,
                        'validityPeriod' => 600
                    ]
                ]
            ];


        // Initialize cURL for API request
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

        // Execute the request
        $response = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        // Check response and handle errors
        if ($response === false) {
            error_log("SMS sending failed. cURL Error: " . curl_error($curl));
            $_SESSION['error'] = "Failed to send SMS. Please try again later.";
        } else {
            $response_data = json_decode($response, true);
            if ($http_code == 200 && isset($response_data['messages'][0]['status']['groupId']) && $response_data['messages'][0]['status']['groupId'] == 1) {
                $_SESSION['success'] = 'OTP sent to your phone number. Please check your messages.';
                $_SESSION['reset_contact'] = $contact_info;
                $_SESSION['reset_time'] = $expiry;
                header('location: reset_verify');
                exit();
            } else {
                error_log("SMS API response error: " . $response);
                $_SESSION['error'] = "Failed to send SMS. Please try again later.";
            }
        }

        curl_close($curl);
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        $_SESSION['error'] = "An error occurred. Please try again later.";
    } finally {
        $pdo->close();
    }

    header('location: another');
    exit();
}
?>


<?php include 'includes/header.php'; ?>
<script src="js/sweetalert.min.js"></script>
<body>
<br><br><br><br>
<div class="container2">
    <a href="password_forgot" style="color: rgb(0, 51, 102);"><i class="fa fa-arrow-left" style="color: rgb(0, 51, 102);"></i></a>
    <center><h2>Reset via SMS</h2></center><br>
    
    <?php
    if (isset($_SESSION['error']) || isset($_SESSION['success'])) {
        $message = isset($_SESSION['error']) ? $_SESSION['error'] : $_SESSION['success'];
        $icon = isset($_SESSION['error']) ? 'error' : 'success';
        echo "
            <script>
                swal({
                    title: '". $message ."',
                    icon: '". $icon ."',
                    button: 'OK'
                });
            </script>
        ";
        unset($_SESSION['error']);
        unset($_SESSION['success']);
    }
    ?>
    
    <form action="another" method="POST">
        <input type="tel" id="contact_info" name="contact_info" 
               placeholder="Enter your number (e.g., 09123456789)" 
               pattern="09[0-9]{9}"
               maxlength="11"
               required><br>
        <button type="submit" class="btn btn-primary btn-block">Send OTP via SMS</button>
    </form>
    <a href="password_forgot" class="button">Use Email Instead</a>
</div>

<script>
// Real-time input validation for Philippine phone number format
document.getElementById('contact_info').addEventListener('input', function(e) {
    let number = this.value.replace(/\D/g, ''); // Remove non-digit characters
    
    // Ensure it starts with '09'
    if (number.length >= 2 && number.substring(0, 2) !== '09') {
        number = '09' + number.substring(2);
    }
    
    // Limit to 11 digits
    number = number.substring(0, 11);
    this.value = number;
});
</script>

<style>
body {
    background: rgb(0, 51, 102);
    background-size: cover;
    background-repeat: no-repeat;
}
.container2 { 
    width: 500px;
    height: 260px;
    margin: 0 auto 50px;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 10px;
    background-color: #f9f9f9;
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
}
.container2 input {
    background-color: #eee;
    border: none;
    margin: 8px 0;
    padding: 10px 15px;
    font-size: 13px;
    border-radius: 8px;
    width: 100%;
    outline: none;
}
.container2 button {
    background-color: #512da8;
    color: #fff;
    font-size: 12px;
    padding: 10px 45px;
    border: 1px solid transparent;
    border-radius: 20px;
    font-weight: 600;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    cursor: pointer;
}
</style>
</body>
</html>