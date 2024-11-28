<?php include 'includes/firewall.php'; ?>
<?php
include 'includes/session.php';

// IP Blocking Functions
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

// Get client IP address
$clientIP = $_SERVER['REMOTE_ADDR'];

// Check if IP is blocked before processing
if (isIPBlocked($clientIP)) {
    // Destroy all sessions and logout
    session_unset();
    session_destroy();
    
    $_SESSION['error'] = 'Access denied. Your IP is temporarily blocked due to security concerns.';
    header('location: login');
    exit();
}



$conn = $pdo->open();

if(isset($_POST['edit'])){
    // Sanitize input fields
    $curr_password = $_POST['curr_password'];
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $firstname = filter_var($_POST['firstname'], FILTER_SANITIZE_STRING);
    $lastname = filter_var($_POST['lastname'], FILTER_SANITIZE_STRING);
    $contact_info = filter_var($_POST['contact_info'], FILTER_SANITIZE_STRING);
    $address = filter_var($_POST['address'], FILTER_SANITIZE_STRING);
    $address2 = filter_var($_POST['address2'], FILTER_SANITIZE_STRING);
    $photo = $_FILES['photo']['name'];

    // Check if any field is empty or consists of only spaces
    if (empty(trim($firstname)) || empty(trim($lastname)) || empty(trim($email)) || empty(trim($password)) || empty(trim($address)) || empty(trim($contact_info))) {
        $_SESSION['error'] = 'Please fill out all required fields properly.';
        header('location: profile');
        exit();
    }

    if(!empty($photo)){
        $file_size = $_FILES['photo']['size'];
        $file_tmp = $_FILES['photo']['tmp_name'];
        $file_type = $_FILES['photo']['type'];
        
        // Check file size - 5MB limit
        if($file_size > 5242880){
            logMaliciousAttempt("Oversized file upload", $clientIP);
            $_SESSION['error'] = 'File size must not exceed 5MB';
            header('location: profile');
            exit();
        }

        // Allowed file types with strict validation
        $allowed_types = array('image/jpeg', 'image/png', 'image/gif');
        if(!in_array($file_type, $allowed_types)){
            logMaliciousAttempt("Invalid file type upload", $clientIP);
            $_SESSION['error'] = 'Only JPG, PNG & GIF files are allowed';
            header('location: profile');
            exit();
        }

        // Enhanced malware scan function
        function scanFile($file, $allowed_types){
            // Advanced malware detection
            $suspicious_patterns = array(
                '<?php',
                '<?=',
                '<script',
                'eval(',
                'exec(',
                'system(',
                'shell_exec(',
                'base64_decode(',
                'gzinflate(',
                'chr(',
                'assert(',
                'create_function(',
                'preg_replace('
            );

            // Read file contents
            $content = file_get_contents($file);

            // Check for suspicious PHP patterns
            foreach($suspicious_patterns as $pattern){
                if(stripos($content, $pattern) !== false){
                    return false;
                }
            }

            // Advanced MIME type checking
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $file);
            finfo_close($finfo);

            // Validate MIME type matches file extension
            $valid_mime = in_array($mime_type, $allowed_types);
            
            // Additional checks
            $file_extension = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
            $valid_extension = in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif']);

            return $valid_mime && $valid_extension;
        }

        // Perform enhanced malware scan
        if(!scanFile($file_tmp, $allowed_types)){
            // Log malicious attempt and block IP
            logMaliciousAttempt("Potential malware in file upload", $clientIP);
            
            // Destroy all sessions and logout
            session_unset();
            session_destroy();
            
            $_SESSION['error'] = 'Malicious file detected. Your account has been temporarily locked.';
            header('location: login');
            exit();
        }

        if(!scanFile($file_tmp, $allowed_types)) {
            $_SESSION['error'] = 'File appears to be malicious or invalid';
            header('location: profile');
            exit();
        }

        // Generate unique filename
        $file_extension = pathinfo($photo, PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $file_extension;
    }

    // Rest of your validation code...

    if(password_verify($curr_password, $user['password'])){
        if(!empty($photo)){
            // Secure file upload
            $upload_path = 'images/' . $filename;
            if(move_uploaded_file($file_tmp, $upload_path)){
                // Set proper permissions
                chmod($upload_path, 0644);
            } else {
                $_SESSION['error'] = 'Error uploading file';
                header('location: profile');
                exit();
            }
        }
        else{
            $filename = $user['photo'];
        }

        if($password == $user['password']){
            $password = $user['password'];
        }
        else{
            $password = hashPassword($password);
        }
        

        try{
            $stmt = $conn->prepare("UPDATE users SET email=:email, password=:password, firstname=:firstname, lastname=:lastname, contact_info=:contact_info, address=:address, address2=:address2, photo=:photo, updated_on=NOW() WHERE id=:id");
            $stmt->execute([
                'email'=>$email, 
                'password'=>$password, 
                'firstname'=>$firstname, 
                'lastname'=>$lastname, 
                'contact_info'=>$contact_info, 
                'address'=>$address, 
                'address2'=>$address2, 
                'photo'=>$filename, 
                'id'=>$user['id']
            ]);

            $_SESSION['success'] = 'Account updated successfully';
        }
        catch(PDOException $e){
            $_SESSION['error'] = $e->getMessage();
        }
    }
    else{
        $_SESSION['error'] = 'Incorrect password';
    }
}
else{
    $_SESSION['error'] = 'Fill up edit form first';
}

$pdo->close();

header('location: profile');
?>