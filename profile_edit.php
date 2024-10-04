<?php
    include 'includes/session.php';

    $conn = $pdo->open();

    if(isset($_POST['edit'])){
        // Sanitize input fields
        $curr_password = htmlspecialchars(strip_tags($_POST['curr_password']));
        $email = htmlspecialchars(strip_tags($_POST['email']));
        $password = htmlspecialchars(strip_tags($_POST['password']));
        $firstname = htmlspecialchars(strip_tags($_POST['firstname']));
        $lastname = htmlspecialchars(strip_tags($_POST['lastname']));
        $contact_info = htmlspecialchars(strip_tags($_POST['contact_info']));
        $address = htmlspecialchars(strip_tags($_POST['address']));
        $address2 = htmlspecialchars(strip_tags($_POST['address2']));
        $photo = $_FILES['photo']['name'];

        // Check if any field is empty or consists of only spaces
        if (empty(trim($firstname)) || empty(trim($lastname)) || empty(trim($email)) || empty(trim($password)) || empty(trim($address)) || empty(trim($contact_info))) {
            $_SESSION['error'] = 'Please fill out all required fields properly.';
            header('location: profile.php');
            exit();
        }

        // Regular expressions for validations
        $specialChars = "/[<>:\/\$;,?!]/";
        $hasUppercase = "/[A-Z]/";
        $hasLowercase = "/[a-z]/";
        $hasNumber = "/[0-9]/";

        // Check email format (@gmail.com)
        if(!preg_match("/^[a-zA-Z0-9._%+-]+@gmail\.com$/", $email)) {
            $_SESSION['error'] = 'Email must be a @gmail.com address';
            header('location: profile.php');
            exit();
        }

        // Check for spaces in email and password
        if (strpos($email, ' ') !== false || strpos($password, ' ') !== false) {
            $_SESSION['error'] = 'Email and password cannot contain spaces';
            header('location: profile.php');
            exit();
        }

        // Check password length
        if (strlen($password) < 8) {
            $_SESSION['error'] = 'Password must be at least 8 characters long';
            header('location: profile.php');
            exit();
        }


        // Password must contain at least one uppercase, one lowercase, and one number; no special characters
        if (!preg_match($hasUppercase, $password) || !preg_match($hasLowercase, $password) || !preg_match($hasNumber, $password) || preg_match($specialChars, $password)) {
            $_SESSION['error'] = 'Password must contain at least one uppercase letter, one lowercase letter, one number, and no special characters.';
            header('location: profile.php');
            exit();
        }

        // Check for special characters in name and address
        if (preg_match($specialChars, $firstname) || preg_match($specialChars, $lastname) || preg_match($specialChars, $address) || preg_match($specialChars, $address2)) {
            $_SESSION['error'] = 'Special characters not allowed';
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
