<?php
include 'includes/session.php';



use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function generateSecureOTP($length = 6) {
    // Cryptographically secure random OTP generation
    $characters = '0123456789';
    $otp = '';
    $max = strlen($characters) - 1;
    
    for ($i = 0; $i < $length; $i++) {
        $otp .= $characters[random_int(0, $max)];
    }
    
    return $otp;
}


function getClientIP() {
    $ipAddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
    } else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else if (isset($_SERVER['HTTP_X_FORWARDED'])) {
        $ipAddress = $_SERVER['HTTP_X_FORWARDED'];
    } else if (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
        $ipAddress = $_SERVER['HTTP_FORWARDED_FOR'];
    } else if (isset($_SERVER['HTTP_FORWARDED'])) {
        $ipAddress = $_SERVER['HTTP_FORWARDED'];
    } else if (isset($_SERVER['REMOTE_ADDR'])) {
        $ipAddress = $_SERVER['REMOTE_ADDR'];
    } else {
        $ipAddress = 'UNKNOWN';
    }
    return $ipAddress;
}

function getMACAddress($ip) {
    // Note: This is a best-effort attempt to get MAC address
    // It will only work on local networks and may not be reliable
    if ($ip == '127.0.0.1' || $ip == '::1') {
        // If on localhost, try to get the local MAC address
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Windows
            ob_start();
            system('ipconfig /all');
            $mycom = ob_get_contents();
            ob_clean();
            $findme = "Physical";
            $pmac = strpos($mycom, $findme);
            $mac = substr($mycom, ($pmac + 36), 17);
        } else {
            // Linux/Unix
            ob_start();
            system('ifconfig');
            $mycom = ob_get_contents();
            ob_clean();
            $findme = "Physical";
            $pmac = strpos($mycom, $findme);
            $mac = substr($mycom, ($pmac + 36), 17);
        }
        return $mac ?: 'Not Available';
    }
    return 'Not Available (Remote Connection)';
}

function getUserLocation() {
    $ip = getClientIP();
    $details = @json_decode(file_get_contents("http://ip-api.com/json/{$ip}"));
    
    if($details && $details->status === 'success') {
        return [
            'city' => $details->city ?? 'Unknown',
            'region' => $details->regionName ?? 'Unknown',
            'country' => $details->country ?? 'Unknown',
            'isp' => $details->isp ?? 'Unknown'
        ];
    }
    
    return [
        'city' => 'Unknown', 
        'region' => 'Unknown', 
        'country' => 'Unknown',
        'isp' => 'Unknown'
    ];
}

function getUserDevice() {
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $deviceInfo = [
        'device' => 'Unknown',
        'os' => 'Unknown',
        'browser' => 'Unknown',
        'model' => 'Unknown' // Added model detection
    ];

    // Device detection
    if (strpos($userAgent, 'Android') !== false) {
        $deviceInfo['device'] = 'Android Device';
        preg_match('/Android\s([0-9\.]*)/i', $userAgent, $matches);
        $deviceInfo['os'] = 'Android ' . ($matches[1] ?? '');

        // Extract phone model for Android
        if (preg_match('/Build\/([a-zA-Z0-9_\-]+)/', $userAgent, $modelMatches)) {
            $deviceInfo['model'] = $modelMatches[1];
        }
    } elseif (strpos($userAgent, 'iPhone') !== false) {
        $deviceInfo['device'] = 'iPhone';
        $deviceInfo['os'] = 'iOS';

        // Attempt to infer iPhone model from the user agent
        if (preg_match('/iPhone.*?OS\s([\d_]+)/', $userAgent, $modelMatches)) {
            $deviceInfo['model'] = 'iPhone (iOS ' . str_replace('_', '.', $modelMatches[1]) . ')';
        }
    } elseif (strpos($userAgent, 'iPad') !== false) {
        $deviceInfo['device'] = 'iPad';
        $deviceInfo['os'] = 'iOS';

        // Attempt to infer iPad model from the user agent
        if (preg_match('/iPad.*?OS\s([\d_]+)/', $userAgent, $modelMatches)) {
            $deviceInfo['model'] = 'iPad (iOS ' . str_replace('_', '.', $modelMatches[1]) . ')';
        }
    } elseif (strpos($userAgent, 'Windows') !== false) {
        $deviceInfo['device'] = 'Windows PC';
        $deviceInfo['os'] = 'Windows';
    } elseif (strpos($userAgent, 'Macintosh') !== false) {
        $deviceInfo['device'] = 'Mac';
        $deviceInfo['os'] = 'macOS';
    } elseif (strpos($userAgent, 'Linux') !== false) {
        $deviceInfo['device'] = 'Linux';
        $deviceInfo['os'] = 'Linux';
    }

    // Browser detection
    if (strpos($userAgent, 'Chrome') !== false) {
        $deviceInfo['browser'] = 'Chrome';
    } elseif (strpos($userAgent, 'Firefox') !== false) {
        $deviceInfo['browser'] = 'Firefox';
    } elseif (strpos($userAgent, 'Safari') !== false) {
        $deviceInfo['browser'] = 'Safari';
    } elseif (strpos($userAgent, 'Edge') !== false) {
        $deviceInfo['browser'] = 'Edge';
    } elseif (strpos($userAgent, 'MSIE') !== false || strpos($userAgent, 'Trident/') !== false) {
        $deviceInfo['browser'] = 'Internet Explorer';
    }

    return $deviceInfo;
}


function sendLoginNotification($email, $firstname, $lastname, $location, $latitude, $longitude) {
    try {
        $mail = new PHPMailer(true);
        $loginTime = date('Y-m-d H:i:s');
        $ipAddress = getClientIP();
        $deviceDetails = getUserDevice(); // Fetch device details
        
        // Server settings
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'overrunssatisa@gmail.com';
        $mail->Password   = 'ahuf cbzv bpph caje';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        $mail->Timeout = 300;
        $mail->SMTPKeepAlive = true;

        // Recipients
        $mail->setFrom('overrunssatisa@gmail.com', 'Overruns Sa Tisa Online Shop');
        $mail->addAddress($email, $firstname . ' ' . $lastname);

        $mail->isHTML(true);
        $mail->Subject = 'New Login to Your Account';

        // Construct email body with device details
        $mail->Body = "
        <h2 style='color: #003366;'>New Login Detected</h2>
        <p>Hello {$firstname} {$lastname},</p>
        <p>We detected a new login to your Overruns Sa Tisa Online Shop account.</p>
        
        <div style='background-color: #f5f5f5; padding: 15px; border-radius: 5px;'>
            <h3 style='color: #003366;'>Login Details:</h3>
            <table style='width: 100%; border-collapse: collapse;'>
                <tr>
                    <td style='padding: 8px; border-bottom: 1px solid #ddd;'><strong>Date and Time:</strong></td>
                    <td style='padding: 8px; border-bottom: 1px solid #ddd;'>{$loginTime}</td>
                </tr>
                <tr>
                    <td style='padding: 8px; border-bottom: 1px solid #ddd;'><strong>Location:</strong></td>
                    <td style='padding: 8px; border-bottom: 1px solid #ddd;'>
                        <a href='https://www.google.com/maps?q={$latitude},{$longitude}' target='_blank'>View on Google Maps</a>
                    </td>
                </tr>
                <tr>
                    <td style='padding: 8px; border-bottom: 1px solid #ddd;'><strong>IP Address:</strong></td>
                    <td style='padding: 8px; border-bottom: 1px solid #ddd;'>{$ipAddress}</td>
                </tr>
                <tr>
                    <td style='padding: 8px; border-bottom: 1px solid #ddd;'><strong>Device:</strong></td>
                    <td style='padding: 8px; border-bottom: 1px solid #ddd;'>
                        {$deviceDetails['device']} ({$deviceDetails['model']})<br>
                        OS: {$deviceDetails['os']}<br>
                        Browser: {$deviceDetails['browser']}
                    </td>
                </tr>
            </table>
        </div>
    
        <p style='color: #ff0000; margin-top: 20px;'>If this wasn't you, please contact us immediately!</p>
        
        <p style='margin-top: 20px;'>Best regards,<br>Overruns Sa Tisa Online Shop Team</p>
    ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Failed to send login notification email: " . $e->getMessage());
        return false;
    }
}

// function sendAdminLoginOTP($contact_info, $firstname) {
//     $otp = generateSecureOTP(); // Ensure this uses a cryptographically secure method
//     $expiry = time() + (10 * 60); // 10 minutes expiry

//     try {
//         $_SESSION['otp'] = [
//             'contact_info' => $contact_info,
//             'otp' => $otp, 
//             'expiry' => $expiry
//         ];

//         $international_format = '+63' . substr($contact_info, 1);
//         $base_url = 'https://wgl14q.api.infobip.com';
//         $api_key = '21e860c23732f2ce85ddeeca882c6fd8-18fe6575-d17f-43ac-a383-55d9d9c8b523';

//         $payload = [
//             'messages' => [
//                 [
//                     'from' => 'OverrunsSaTisa',
//                     'destinations' => [
//                         ['to' => $international_format]
//                     ],
//                     'text' => "Hello {$firstname}, Your Admin Login OTP is: {$otp}. This code expires in 10 minutes.",
//                     'flash' => false,
//                     'validityPeriod' => 600
//                 ]
//             ]
//         ];

//         // Send SMS via cURL
//         $curl = curl_init();
//         curl_setopt_array($curl, [
//             CURLOPT_URL => $base_url . '/sms/2/text/advanced',
//             CURLOPT_RETURNTRANSFER => true,
//             CURLOPT_HTTPHEADER => [
//                 'Content-Type: application/json',
//                 'Authorization: App ' . $api_key
//             ],
//             CURLOPT_POST => true,
//             CURLOPT_POSTFIELDS => json_encode($payload),
//             CURLOPT_TIMEOUT => 30
//         ]);

//         $response = curl_exec($curl);
//         $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
//         $curl_error = curl_error($curl);
//         curl_close($curl);

//         if ($response === false) {
//             error_log("SMS sending failed. cURL Error: " . $curl_error);
//             return false;
//         }

//         $response_data = json_decode($response, true);

//         if (!($http_code === 200 && 
//               isset($response_data['messages'][0]['status']['groupId']) && 
//               $response_data['messages'][0]['status']['groupId'] === 1)) {
//             error_log("SMS sending failed. Response: " . print_r($response_data, true));
//             return false;
//         }

//         return true;

//     } catch (Exception $e) {
//         error_log("Error sending admin login OTP: " . $e->getMessage());
//         return false;
//     }
// }

function sendAdminLoginOTP($email, $firstname) {
    $otp = generateSecureOTP(); // Generate a secure OTP
    $expiry = time() + (10 * 60); // 10 minutes expiry

    try {
        $_SESSION['otp'] = [
            'contact_info' => $email, // Changed from contact_info to email
            'otp' => $otp,
            'expiry' => $expiry
        ];

        $mail = new PHPMailer(true);

        // Server settings
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'overrunssatisa@gmail.com';
        $mail->Password   = 'ahuf cbzv bpph caje';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        $mail->setFrom('overrunssatisa@gmail.com', 'Overruns Sa Tisa Online Shop');
        $mail->addAddress($email, $firstname);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Your Admin Login OTP Code';
        $mail->Body = "
            <h2>Hello, {$firstname}!</h2>
            <p>Your Admin Login OTP is:</p>
            <h3 style='color: #003366;'>{$otp}</h3>
            <p>This code will expire in 10 minutes.</p>
            <p>If you did not request this code, please contact support immediately.</p>
            <p>Best regards,<br>Your App Team</p>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Failed to send OTP email: " . $e->getMessage());
        return false;
    }
}

$conn = null;
$stmt = null;
try {
    $conn = $pdo->open();

    if(isset($_POST['login'])) {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];

        if(!$email || !$password) {
            throw new Exception('Invalid input');
        }

        $stmt = $conn->prepare("SELECT *, COUNT(*) AS numrows FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row && $row['numrows'] > 0) {
            $currentTime = time();
            $lockoutExpired = ($row['lockout_time'] > 0 && $currentTime > $row['lockout_time']);

            if ($lockoutExpired && $row['login_attempts'] >= 10) {
                // Reset login attempts and lockout time if 12 hours have passed
                $resetAttempts = $conn->prepare("UPDATE users SET 
                    login_attempts = 0, 
                    lockout_time = 0 
                WHERE email = :email");
                $resetAttempts->bindParam(':email', $email, PDO::PARAM_STR);
                $resetAttempts->execute();

                // Refresh the row data after reset
                $stmt = $conn->prepare("SELECT *, COUNT(*) AS numrows FROM users WHERE email = :email");
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
            }

            // Check if account is currently locked
            if ($row['lockout_time'] > $currentTime) {
                $remainingSeconds = $row['lockout_time'] - $currentTime;
                $hours = floor($remainingSeconds / 3600);
                $minutes = floor(($remainingSeconds % 3600) / 60);
                $seconds = $remainingSeconds % 60;

                // Format the message to show precise remaining time
                $timeMessage = [];
                if ($hours > 0) $timeMessage[] = "$hours hour" . ($hours != 1 ? 's' : '');
                if ($minutes > 0) $timeMessage[] = "$minutes minute" . ($minutes != 1 ? 's' : '');
                if ($seconds > 0) $timeMessage[] = "$seconds second" . ($seconds != 1 ? 's' : '');

                $remainingTimeString = implode(' ', $timeMessage);

                $_SESSION['error'] = "Account is locked. Please try again in {$remainingTimeString}.";
                header('Location: login');
                exit();
            }


            if($row['status'] == 1) {
                if(password_verify($password, $row['password'])) {
                    // Reset login attempts on successful login
                    $resetAttempts = $conn->prepare("UPDATE users SET login_attempts = 0, lockout_time = 0 WHERE email = :email");
                    $resetAttempts->bindParam(':email', $email, PDO::PARAM_STR);
                    $resetAttempts->execute();

                        // Get latitude and longitude from POST data
                        $latitude = $_POST['latitude'] ?? null;
                        $longitude = $_POST['longitude'] ?? null;
                        
                        // Save the location data
                        if ($latitude && $longitude) {
                            $stmt = $conn->prepare("INSERT INTO user_locations (user_id, latitude, longitude, first_login, last_login) 
                                                    VALUES (:user_id, :latitude, :longitude, NOW(), NOW()) 
                                                    ON DUPLICATE KEY UPDATE latitude = :latitude, longitude = :longitude, last_login = NOW()");
                            $stmt->bindParam(':user_id', $row['id'], PDO::PARAM_INT);
                            $stmt->bindParam(':latitude', $latitude, PDO::PARAM_STR);
                            $stmt->bindParam(':longitude', $longitude, PDO::PARAM_STR);
                            $stmt->execute();
                        }

                    // Send login notification email
                    sendLoginNotification($email, $row['firstname'], $row['lastname'], $location, $latitude, $longitude);

                    if($row['type'] == 1 || $row['type'] == 2) { // Admin user
                        // Send OTP via email to the user's email address
                        $otp_sent = sendAdminLoginOTP($email, $row['firstname']);
                        
                        if ($otp_sent) {
                            // Store temporary session data for OTP verification
                            $_SESSION['admin_login_email'] = $email;
                            $_SESSION['admin_login_firstname'] = $row['firstname']; // Add this line
                            header('Location: admin_otp_verify');
                            exit();
                        } else {
                            $_SESSION['error'] = 'Failed to send OTP email. Please try again.';
                            header('Location: login');
                            exit();
                        }
                    } else {
                        // Regular user login process remains the same
                        setSessionVariables($row);
                        $redirect = 'profile';
                        $_SESSION['success'] = 'Login successful';
                        header("Location: $redirect");
                        exit();
                    }
                } else {
                    // Increment login attempts
                    $attempts = $row['login_attempts'] + 1;
                    $lockoutTime = 0;
                    $message = '';

                    // Tiered lockout system
                    if($attempts >= 8) {
                        $lockoutTime = time() + (3 * 60); // 3 minutes lockout
                        $attempts = 0;
                        $message = 'Too many failed attempts. Account locked for 3 minutes.';
                    } elseif($attempts >= 5) {
                        $lockoutTime = time() + (1 * 60); // 1 minute lockout
                        $message = 'Too many failed attempts. Account locked for 1 minute.';
                    } else {
                        $remainingAttempts = 5 - $attempts;
                        $message = "Incorrect Password. {$remainingAttempts} attempts remaining before temporary lockout.";
                    }

                    $updateAttempts = $conn->prepare("UPDATE users SET login_attempts = :attempts, lockout_time = :lockout WHERE email = :email");
                    $updateAttempts->bindParam(':attempts', $attempts, PDO::PARAM_INT);
                    $updateAttempts->bindParam(':lockout', $lockoutTime, PDO::PARAM_INT);
                    $updateAttempts->bindParam(':email', $email, PDO::PARAM_STR);
                    $updateAttempts->execute();

                    $_SESSION['error'] = $message;
                }
            } elseif($row['status'] == 2) {
                $_SESSION['error'] = 'Please verify your email address before logging in.';
            } elseif($row['status'] == 5) {
                $_SESSION['error'] = 'Sorry your account has been declined. Check your email for the reason.';
            } elseif ($row['status'] == 3) {
                $_SESSION['error'] = 'Please wait for admin approval.';
            } else {
                $_SESSION['error'] = 'Account Deactivated.';
            }
        } else {
            $_SESSION['error'] = 'Email Not Found. Please sign up first.';
        }
    } else {
        $_SESSION['error'] = 'Input login credentials first';
    }
} catch(PDOException $e) {
    $_SESSION['error'] = 'Database error occurred. Please try again later.';
    error_log('Database error in login: ' . $e->getMessage());
} catch(Exception $e) {
    $_SESSION['error'] = 'An error occurred. Please try again.';
    error_log('Error in login: ' . $e->getMessage());
} finally {
    if($stmt) {
        $stmt = null;
    }
    if($conn) {
        $pdo->close();
    }
}

header('Location: login');
exit();

function setSessionVariables($userData) {
    $_SESSION[$userData['type'] ? 'admin' : 'user'] = $userData['id'];
    $_SESSION['success'] = 'Login successful';
}
?>