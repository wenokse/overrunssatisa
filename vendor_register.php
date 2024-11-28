<?php include 'includes/firewall.php'; ?>
<?php
include 'includes/session.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function blockIPAddress($ipAddress, $blockDuration = 86400) { // 86400 seconds = 24 hours
    $blockedIPsFile = __DIR__ . '/blocked_ips.json';
    
    // Read existing blocked IPs
    $blockedIPs = file_exists($blockedIPsFile) 
        ? json_decode(file_get_contents($blockedIPsFile), true) 
        : [];
    
    // Remove expired blocks
    $currentTime = time();
    $blockedIPs = array_filter($blockedIPs, function($blockTime) use ($currentTime) {
        return $currentTime - $blockTime < 86400;
    }, ARRAY_FILTER_USE_KEY);
    
    // Add new block
    $blockedIPs[$ipAddress] = time();
    
    // Save updated blocked IPs
    file_put_contents($blockedIPsFile, json_encode($blockedIPs));
}

function isIPBlocked($ipAddress) {
    $blockedIPsFile = __DIR__ . '/blocked_ips.json';
    
    if (!file_exists($blockedIPsFile)) {
        return false;
    }
    
    $blockedIPs = json_decode(file_get_contents($blockedIPsFile), true);
    
    // Check if IP is blocked and block is still valid
    if (isset($blockedIPs[$ipAddress])) {
        $blockTime = $blockedIPs[$ipAddress];
        $currentTime = time();
        
        // If block is less than 24 hours old, it's still active
        if ($currentTime - $blockTime < 86400) {
            return true;
        }
    }
    
    return false;
}

function logMaliciousAttempt($reason, $ipAddress) {
    $logFile = __DIR__ . '/security_log.txt';
    $logEntry = date('Y-m-d H:i:s') . " | IP: $ipAddress | Reason: $reason\n";
    file_put_contents($logFile, $logEntry, FILE_APPEND);
    
    // Block the IP
    blockIPAddress($ipAddress);
}

function containsSpecialCharacters($str) {
    return preg_match('/[<>:\/\$\;\,\?\!]/', $str);
}


function detectPHPShellAttempt($filePath, $ipAddress) {
    $shellDetectionRules = [
        '/(\b(passthru|system|shell_exec|exec|popen|proc_open|proc_close)\b)/i',
        '/base64_decode\s*\(/i',
        '/(eval|assert|create_function|preg_replace)\s*\(/i',
        '/(\$_\w+\s*=\s*\$_(GET|POST|REQUEST|SERVER)\[)/i',
        '/(\\\x[0-9a-fA-F]{2}|\\\[0-7]{3})/i',
        '/(<\?php.*?(\b(exec|system|passthru)\b).*?\?>)/is',
        '/(\binclude\s*\(\s*\$_\w+\s*\)|\brequire\s*\(\s*\$_\w+\s*\))/i'
    ];

    $fileContents = file_get_contents($filePath);
    $compressedContents = preg_replace('/\s+/', '', $fileContents);
    foreach ($shellDetectionRules as $rule) {
        if (preg_match($rule, $fileContents) || 
            preg_match($rule, $compressedContents)) {
            return true;
        }
    }

    // Check file extension and magic bytes
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $filePath);
    finfo_close($finfo);

    // Strict type checking
    $allowedMimeTypes = [
        'image/jpeg', 
        'image/png', 
        'image/gif', 
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ];

    // Check file extension
    $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'docx', 'doc'];

    // Block PHP and potentially dangerous extensions
    $dangerousExtensions = ['php', 'php3', 'php4', 'php5', 'php7', 'phtml', 'phps', 'phar', 'asp', 'aspx', 'jsp'];
    
    if (in_array($extension, $dangerousExtensions)) {
        return true;
    }

    // Additional file content signature checks
    $dangerousSignatures = [
        '/<\?php/i',           // PHP tags
        '/eval\s*\(/i',        // Eval function
        '/base64_decode/i',    // Base64 decoding
        '/system\s*\(/i',      // System command execution
        '/exec\s*\(/i',        // Exec function
        '/shell_exec/i',       // Shell execution
        '/proc_open/i',        // Process opening
        '/passthru/i',         // Pass-through execution
        '/base64_decode/i',
        '/gzinflate/i',
        '/chr/i',
        '/assert/i',
        '/create_function\s*\(/i', 
        '/preg_replace\s*\(/i', 
        '/\$_\w+\s*=\s*\$_(GET|POST|REQUEST|SERVER)/i' // Variable assignment from superglobals
    ];

    foreach ($dangerousSignatures as $signature) {
        if (strpos($fileContents, $signature) !== false) {
            throw new Exception("Potential malicious content detected in file.");
            return true;
        }
    }

    return false;
}

function secureFileConversion($inputFile, $outputFormat) {
    // Sanitize and validate file conversion
    $safeOutputFormats = ['jpg', 'pdf', 'docx'];
    
    if (!in_array($outputFormat, $safeOutputFormats)) {
        return false;
    }

    // Prevent shell injection by using escapeshellarg()
    $escapedInput = escapeshellarg($inputFile);
    $outputDir = escapeshellarg(dirname($inputFile));

    // Use LibreOffice for safe conversion
    switch ($outputFormat) {
        case 'jpg':
            $command = "convert $escapedInput " . escapeshellarg(dirname($inputFile) . '/converted_' . time() . '.jpg');
            break;
        case 'pdf':
            $command = "libreoffice --convert-to pdf $escapedInput --outdir $outputDir";
            break;
        case 'docx':
            $command = "libreoffice --convert-to docx $escapedInput --outdir $outputDir";
            break;
    }

    // Execute conversion with limited shell access
    exec($command, $output, $returnVar);
    
    return $returnVar === 0;
}


if(isset($_POST['signup'])){
    $store = $_POST['store'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $address = $_POST['address'];
    $address2 = $_POST['address2'];
    $contact_info = $_POST['contact_info'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $repassword = $_POST['repassword'];
    $tin_number = $_POST['tin_number'];

    $clientIP = $_SERVER['REMOTE_ADDR'];

    // First, check if IP is already blocked
    if (isIPBlocked($clientIP)) {
       $_SESSION['error'] = 'Access denied. Your IP is temporarily blocked due to security concerns.';
       header('location: vendor_signup');
       exit();
   }

    if (isset($_POST['recaptcha_response'])) {
        $recaptchaResponse = $_POST['recaptcha_response'];
        $secretKey = '6Lf-VoIqAAAAALGiTwK15qjAKTRD6Kv8al322Apf';
        
        $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$recaptchaResponse}");
        $responseKeys = json_decode($response, true);
        
        if (!$responseKeys['success'] || $responseKeys['score'] < 0.5) {
            $_SESSION['error'] = 'reCAPTCHA verification failed. Please try again.';
            header('Location: vendor_signup');
            exit();
        }
    }

    // Validate for special characters
    if (containsSpecialCharacters($firstname) || containsSpecialCharacters($lastname) || 
        containsSpecialCharacters($email) || containsSpecialCharacters($password) || 
        containsSpecialCharacters($store)) {
        $_SESSION['error'] = 'Special characters like <>:/$;,?! are not allowed.';
        header('location: vendor_signup');
        exit();
    }

    // Validate Gmail address
    if (strpos($email, '@gmail.com') === false) {
        $_SESSION['error'] = 'Email must be a @gmail.com address';
        header('location: vendor_signup');
        exit();
    }

    // Password match validation
    if($password != $repassword){
        $_SESSION['error'] = 'Passwords did not match';
        header('location: vendor_signup');
        exit();
    }

    try {
        $required_files = ['photo', 'valid_id', 'bir_doc', 'dti_doc', 'mayor_permit'];
        foreach ($required_files as $file) {
            if(!isset($_FILES[$file]) || $_FILES[$file]['error'] === UPLOAD_ERR_NO_FILE) {
                throw new Exception(ucfirst(str_replace('_', ' ', $file)) . ' is required');
            }

            $filePath = $_FILES[$file]['tmp_name'];
            
            // Aggressive PHP Shell Detection
            if (detectPHPShellAttempt($filePath)) {
                // Log potential attack attempt
                error_log("Potential PHP Shell Upload Detected: " . $_FILES[$file]['name']);
                
                // Immediately reject and log
                throw new Exception("Potential malicious file detected. Upload rejected.");
            }

            // Safe file conversion
            $convertedFilePath = false;
            if (in_array($file, ['photo', 'valid_id'])) {
                $convertedFilePath = secureFileConversion($filePath, 'jpg');
            } else {
                $convertedFilePath = secureFileConversion($filePath, 'pdf');
            }

            if (!$convertedFilePath) {
                throw new Exception("File conversion failed for $file");
            }

            // Update file path with converted file
            $_FILES[$file]['tmp_name'] = $convertedFilePath;

        }
        $requiredFilesRules = [
            'photo' => ['mime' => ['image/jpeg', 'image/png'], 'max_size' => 5 * 1024 * 1024], // 5MB
            'valid_id' => ['mime' => ['image/jpeg', 'image/png', 'application/pdf'], 'max_size' => 10 * 1024 * 1024], // 10MB
            'bir_doc' => ['mime' => ['application/pdf'], 'max_size' => 10 * 1024 * 1024],
            'dti_doc' => ['mime' => ['application/pdf'], 'max_size' => 10 * 1024 * 1024],
            'mayor_permit' => ['mime' => ['application/pdf'], 'max_size' => 10 * 1024 * 1024]
        ];

        foreach ($required_files as $file) {
            // Check file size and MIME type
            $uploadedFile = $_FILES[$file];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $uploadedFile['tmp_name']);
            finfo_close($finfo);

            // Validate MIME type
            if (!in_array($mimeType, $requiredFilesRules[$file]['mime'])) {
                logMaliciousAttempt("Invalid File Type: $file", $clientIP);
                throw new Exception("Invalid file type for $file");
            }

            // Validate file size
            if ($uploadedFile['size'] > $requiredFilesRules[$file]['max_size']) {
                logMaliciousAttempt("File Too Large: $file", $clientIP);
                throw new Exception("File $file exceeds maximum allowed size");
            }

            // Perform shell detection with IP address
            if (detectPHPShellAttempt($uploadedFile['tmp_name'], $clientIP)) {
                throw new Exception("Malicious file content detected");
            }
        }

        $conn = $pdo->open();

        // Check if email exists
        $stmt = $conn->prepare("SELECT COUNT(*) AS numrows FROM users WHERE email=:email");
        $stmt->execute(['email'=>$email]);
        $row = $stmt->fetch();

        if($row['numrows'] > 0){
            throw new Exception('Email already taken');
        }

        // Generate verification code
        $verification_code = sprintf("%06d", mt_rand(1, 999999));

        // Send verification email
        $mail = new PHPMailer(true);

        //Server settings
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
        
        //Recipients
        $mail->setFrom('overrunssatisa@gmail.com', 'Overruns Sa Tisa Online Shop');
        $mail->addAddress($email, $firstname . ' ' . $lastname);

        //Content
        $mail->isHTML(true);
        $mail->Subject = 'Vendor Registration - Email Verification Code';
        $mail->Body    = "<h2>Thank you for registering as a vendor with our shop.<br>Your verification code is: <b>$verification_code</b></h2>";

        $mail->send();

        // Store all data in session including file data
        $_SESSION['temp_vendor_data'] = [
            'store' => $store,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'address' => $address,
            'address2' => $address2,
            'contact_info' => $contact_info,
            'email' => $email,
            'password' => $password,
            'tin_number' => $tin_number,
            'verification_code' => $verification_code,
            'code_time' => time()
        ];

        // Store file data separately to avoid potential session size issues
        $_SESSION['temp_vendor_files'] = [];
        foreach ($required_files as $file) {
            $_SESSION['temp_vendor_files'][$file] = [
                'name' => $_FILES[$file]['name'],
                'type' => $_FILES[$file]['type'],
                'tmp_name' => $_FILES[$file]['tmp_name'],
                'error' => $_FILES[$file]['error'],
                'size' => $_FILES[$file]['size']
            ];
            
            // Move uploaded file to temporary location
            $temp_dir = sys_get_temp_dir() . '/vendor_uploads/';
            if (!is_dir($temp_dir)) {
                mkdir($temp_dir, 0777, true);
            }
            
            $temp_file = $temp_dir . uniqid() . '_' . basename($_FILES[$file]['name']);
            if (!move_uploaded_file($_FILES[$file]['tmp_name'], $temp_file)) {
                throw new Exception("Error moving uploaded file: " . $_FILES[$file]['name']);
            }
            $_SESSION['temp_vendor_files'][$file]['temp_path'] = $temp_file;
        }

        $_SESSION['success'] = 'Verification code sent to your email. Please check your inbox.';
        header('location: verify_vendor_email');
        exit();

    } catch (Exception $e) {
        error_log("Error in vendor registration: " . $e->getMessage());
        logMaliciousAttempt($e->getMessage(), $clientIP);
        $_SESSION['error'] = $e->getMessage();
        header('location: vendor_signup');
        exit();
    } finally {
        if(isset($conn)) {
            $pdo->close();
        }
    }
} else {
    $_SESSION['error'] = 'Fill up signup form first';
    header('location: vendor_signup');
    exit();
}
?>