<?php
include 'includes/session.php';

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

    // Validate file upload
    if(!empty($photo)){
        $file_size = $_FILES['photo']['size'];
        $file_tmp = $_FILES['photo']['tmp_name'];
        $file_type = $_FILES['photo']['type'];
        
        // Check file size - 5MB limit
        if($file_size > 5242880){
            $_SESSION['error'] = 'File size must not exceed 5MB';
            header('location: profile');
            exit();
        }

        // Check file type
        $allowed_types = array('image/jpeg', 'image/png', 'image/gif');
        if(!in_array($file_type, $allowed_types)){
            $_SESSION['error'] = 'Only JPG, PNG & GIF files are allowed';
            header('location: profile');
            exit();
        }

        // Basic malware scan
        function scanFile($file){
            // Check for PHP code in the file
            $content = file_get_contents($file);
            $suspicious_patterns = array(
                '<?php',
                '<?=',
                '<script',
                'eval(',
                'exec(',
                'system(',
                'shell_exec(',
                'base64_decode(',
                'gzinflate('
            );

            foreach($suspicious_patterns as $pattern){
                if(stripos($content, $pattern) !== false){
                    return false;
                }
            }

            // Check file headers
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $file);
            finfo_close($finfo);

            return in_array($mime_type, $allowed_types);
        }

        // Perform malware scan
        if(!scanFile($file_tmp)){
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