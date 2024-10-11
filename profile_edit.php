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
    if (empty(trim($firstname)) || empty(trim($lastname)) || empty(trim($password)) || empty(trim($address)) || empty(trim($contact_info))) {
        $_SESSION['error'] = 'Please fill out all required fields properly.';
        header('location: profile.php');
        exit();
    }

    // Check email format (@gmail.com)
    if(!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match("/@gmail\.com$/", $email)) {
        $_SESSION['error'] = 'Email must be a valid @gmail.com address';
        header('location: profile.php');
        exit();
    }

    // Check for spaces in email and password
    if (strpos($email, ' ') !== false || strpos($password, ' ') !== false) {
        $_SESSION['error'] = 'Email and password cannot contain spaces';
        header('location: profile.php');
        exit();
    }

    // Check password length and complexity
    if (strlen($password) < 8 || !preg_match("/[A-Z]/", $password) || !preg_match("/[a-z]/", $password) || !preg_match("/[0-9]/", $password) || preg_match("/[^A-Za-z0-9]/", $password)) {
        $_SESSION['error'] = 'Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one number, and no special characters.';
        header('location: profile.php');
        exit();
    }

    // Validate phone number
    if (!preg_match("/^[0-9]{11}$/", $contact_info)) {
        $_SESSION['error'] = 'Phone number must be exactly 11 digits long.';
        header('location: profile.php');
        exit();
    }

    if(password_verify($curr_password, $user['password'])){
        if(!empty($photo)){
            move_uploaded_file($_FILES['photo']['tmp_name'], 'images/'.$photo);
            $filename = $photo;    
        }
        else{
            $filename = $user['photo'];
        }

        // Hash the new password if it has been changed
        if($password == $user['password']){
            $password = $user['password'];
        }
        else{
            $password = password_hash($password, PASSWORD_DEFAULT);
        }

        try{
            $stmt = $conn->prepare("UPDATE users SET email=:email, password=:password, firstname=:firstname, lastname=:lastname, contact_info=:contact_info, address=:address, address2=:address2, photo=:photo WHERE id=:id");
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

header('location: profile.php');
?>