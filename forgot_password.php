<?php
include 'includes/session.php';
include 'includes/conn.php';

if(isset($_POST['forgot'])){
    $email = $_POST['email'];

    $stmt = $conn->prepare("SELECT *, COUNT(*) AS numrows FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $row = $stmt->fetch();

    if($row['numrows'] > 0){
        // Generate a random 6-digit code
        $reset_code = sprintf("%06d", mt_rand(1, 999999));

        // Update the user's reset_code in the database
        $stmt = $conn->prepare("UPDATE users SET reset_code = :reset_code WHERE email = :email");
        $stmt->execute(['reset_code' => $reset_code, 'email' => $email]);

        // Send SMS with the reset code
        $contact_info = $row['contact_info'];
        sendSMS($contact_info, "Your password reset code is: " . $reset_code);

        $_SESSION['success'] = 'Password reset code sent to your phone number.';
    }
    else{
        $_SESSION['error'] = 'Email not found';
    }
}

header('location: password_forgot.php');

function sendSMS($phone_number, $message) {
    // Implement your SMS sending logic here
    // You'll need to use an SMS gateway API
    // For example, using Twilio:
    
    // require_once 'path/to/twilio-php/autoload.php';
    // use Twilio\Rest\Client;
    
    // $account_sid = 'your_account_sid';
    // $auth_token = 'your_auth_token';
    // $twilio_number = 'your_twilio_number';
    
    // $client = new Client($account_sid, $auth_token);
    // $client->messages->create(
    //     $phone_number,
    //     array(
    //         'from' => $twilio_number,
    //         'body' => $message
    //     )
    // );
}
?>