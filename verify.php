<?php
include 'includes/session.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

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
        'browser' => 'Unknown'
    ];
    
    // Device detection
    if(strpos($userAgent, 'Android') !== false) {
        $deviceInfo['device'] = 'Android Device';
        preg_match('/Android\s([0-9\.]*)/i', $userAgent, $matches);
        $deviceInfo['os'] = 'Android ' . ($matches[1] ?? '');
    } elseif(strpos($userAgent, 'iPhone') !== false) {
        $deviceInfo['device'] = 'iPhone';
        $deviceInfo['os'] = 'iOS';
    } elseif(strpos($userAgent, 'iPad') !== false) {
        $deviceInfo['device'] = 'iPad';
        $deviceInfo['os'] = 'iOS';
    } elseif(strpos($userAgent, 'Windows') !== false) {
        $deviceInfo['device'] = 'Windows PC';
        $deviceInfo['os'] = 'Windows';
    } elseif(strpos($userAgent, 'Macintosh') !== false) {
        $deviceInfo['device'] = 'Mac';
        $deviceInfo['os'] = 'macOS';
    } elseif(strpos($userAgent, 'Linux') !== false) {
        $deviceInfo['device'] = 'Linux';
        $deviceInfo['os'] = 'Linux';
    }
    
    // Browser detection
    if(strpos($userAgent, 'Chrome') !== false) {
        $deviceInfo['browser'] = 'Chrome';
    } elseif(strpos($userAgent, 'Firefox') !== false) {
        $deviceInfo['browser'] = 'Firefox';
    } elseif(strpos($userAgent, 'Safari') !== false) {
        $deviceInfo['browser'] = 'Safari';
    } elseif(strpos($userAgent, 'Edge') !== false) {
        $deviceInfo['browser'] = 'Edge';
    } elseif(strpos($userAgent, 'MSIE') !== false || strpos($userAgent, 'Trident/') !== false) {
        $deviceInfo['browser'] = 'Internet Explorer';
    }
    
    return $deviceInfo;
}

function sendLoginNotification($email, $firstname, $lastname) {
    try {
        $mail = new PHPMailer(true);
        $location = getUserLocation();
        $deviceInfo = getUserDevice();
        $loginTime = date('Y-m-d H:i:s');
        $ipAddress = getClientIP();
        $macAddress = getMACAddress($ipAddress);
        
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

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'New Login to Your Account';
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
                        <td style='padding: 8px; border-bottom: 1px solid #ddd;'><strong>IP Address:</strong></td>
                        <td style='padding: 8px; border-bottom: 1px solid #ddd;'>{$ipAddress}</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px; border-bottom: 1px solid #ddd;'><strong>MAC Address:</strong></td>
                        <td style='padding: 8px; border-bottom: 1px solid #ddd;'>{$macAddress}</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px; border-bottom: 1px solid #ddd;'><strong>Device:</strong></td>
                        <td style='padding: 8px; border-bottom: 1px solid #ddd;'>{$deviceInfo['device']}</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px; border-bottom: 1px solid #ddd;'><strong>Operating System:</strong></td>
                        <td style='padding: 8px; border-bottom: 1px solid #ddd;'>{$deviceInfo['os']}</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px; border-bottom: 1px solid #ddd;'><strong>Browser:</strong></td>
                        <td style='padding: 8px; border-bottom: 1px solid #ddd;'>{$deviceInfo['browser']}</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px; border-bottom: 1px solid #ddd;'><strong>Location:</strong></td>
                        <td style='padding: 8px; border-bottom: 1px solid #ddd;'>{$location['city']}, {$location['region']}, {$location['country']}</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px; border-bottom: 1px solid #ddd;'><strong>Internet Provider:</strong></td>
                        <td style='padding: 8px; border-bottom: 1px solid #ddd;'>{$location['isp']}</td>
                    </tr>
                </table>
            </div>

            <p style='color: #ff0000; margin-top: 20px;'>If this wasn't you, please contact us immediately!</p>
            
            <p style='margin-top: 20px;'>Best regards,<br>Overruns Sa Tisa Online Shop Team</p>
            
            <div style='font-size: 12px; color: #666; margin-top: 20px;'>
                This is an automated security notification. Please do not reply to this email.
            </div>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Failed to send login notification email: " . $e->getMessage());
        return false;
    }
}

$conn = null;
$stmt = null;

try {
    $conn = $pdo->open();

    if (isset($_POST['login'])) {
        // Sanitize and validate input
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];
        $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';

        if (!$email || !$password || !$recaptchaResponse) {
            $_SESSION['error'] = 'All fields are required.';
            header('Location: login');
            exit();
        }

        // Verify reCAPTCHA
        require('recaptcha/src/autoload.php');
        $secretKey = '6Lf-VoIqAAAAAIXG5tzEBzI814o8JbZVs61dfiVk';
        $recaptcha = new \ReCaptcha\ReCaptcha($secretKey, new \ReCaptcha\RequestMethod\SocketPost());
        $resp = $recaptcha->verify($recaptchaResponse, $_SERVER['REMOTE_ADDR']);

        if (!$resp->isSuccess() || $resp->getScore() < 0.5) {
            $_SESSION['error'] = 'Failed reCAPTCHA verification. Please try again.';
            header('Location: login');
            exit();
        }

        // Check user account
        $stmt = $conn->prepare("SELECT *, COUNT(*) AS numrows FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && $row['numrows'] > 0) {
            // Check account status
            if ($row['lockout_time'] > time()) {
                $remainingTime = ceil(($row['lockout_time'] - time()) / 60);
                $_SESSION['error'] = "Account locked. Try again in {$remainingTime} minute(s).";
                header('Location: login');
                exit();
            }

            if ($row['status'] == 1) {
                if (password_verify($password, $row['password'])) {
                    // Reset login attempts
                    $resetAttempts = $conn->prepare("UPDATE users SET login_attempts = 0, lockout_time = 0 WHERE email = :email");
                    $resetAttempts->bindParam(':email', $email, PDO::PARAM_STR);
                    $resetAttempts->execute();

                    // Set session and redirect
                    setSessionVariables($row);
                    sendLoginNotification($email, $row['firstname'], $row['lastname']);
                    $redirect = $row['type'] ? 'admin/home' : 'profile';
                    $_SESSION['success'] = 'Login successful';
                    header("Location: $redirect");
                    exit();
                } else {
                    // Handle failed login attempts
                    handleFailedLogin($row, $conn);
                }
            } elseif ($row['status'] == 0) {
                $_SESSION['error'] = 'Please verify your email address before logging in.';
            } elseif ($row['status'] == 5) {
                $_SESSION['error'] = 'Your account has been declined. Check your email for details.';
            } elseif ($row['status'] == 3) {
                $_SESSION['error'] = 'Please wait for admin approval.';
            } else {
                $_SESSION['error'] = 'Account is deactivated.';
            }
        } else {
            $_SESSION['error'] = 'Email not found. Please register first.';
        }
    } else {
        $_SESSION['error'] = 'Please enter your login credentials.';
    }
} catch (PDOException $e) {
    $_SESSION['error'] = 'Database error occurred. Please try again later.';
    error_log('Database error: ' . $e->getMessage());
} catch (Exception $e) {
    $_SESSION['error'] = 'An error occurred. Please try again.';
    error_log('Error: ' . $e->getMessage());
} finally {
    if ($stmt) {
        $stmt = null;
    }
    if ($conn) {
        $pdo->close();
    }
}

header('Location: login');
exit();

function handleFailedLogin($row, $conn) {
    $attempts = $row['login_attempts'] + 1;
    $lockoutTime = 0;
    $message = '';

    if ($attempts >= 8) {
        $lockoutTime = time() + (3 * 60); // 3 minutes lockout
        $attempts = 0;
        $message = 'Too many failed attempts. Account locked for 3 minutes.';
    } elseif ($attempts >= 5) {
        $lockoutTime = time() + (1 * 60); // 1 minute lockout
        $message = 'Too many failed attempts. Account locked for 1 minute.';
    } else {
        $remainingAttempts = 5 - $attempts;
        $message = "Incorrect Password. {$remainingAttempts} attempts remaining before temporary lockout.";
    }

    $updateAttempts = $conn->prepare("UPDATE users SET login_attempts = :attempts, lockout_time = :lockout WHERE email = :email");
    $updateAttempts->bindParam(':attempts', $attempts, PDO::PARAM_INT);
    $updateAttempts->bindParam(':lockout', $lockoutTime, PDO::PARAM_INT);
    $updateAttempts->bindParam(':email', $row['email'], PDO::PARAM_STR);
    $updateAttempts->execute();

    $_SESSION['error'] = $message;
    header('Location: login');
    exit();
}

function setSessionVariables($userData) {
    $_SESSION[$userData['type'] ? 'admin' : 'user'] = $userData['id'];
}
?>