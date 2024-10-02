<?php
    include 'includes/session.php';

    $conn = $pdo->open();

    if(isset($_POST['edit'])){
        // Anti-scripting: Strip tags and special character sanitization
        $curr_password = htmlspecialchars(strip_tags($_POST['curr_password']));
        $email = htmlspecialchars(strip_tags($_POST['email']));
        $password = htmlspecialchars(strip_tags($_POST['password']));
        $firstname = htmlspecialchars(strip_tags($_POST['firstname']));
        $lastname = htmlspecialchars(strip_tags($_POST['lastname']));
        $contact_info = htmlspecialchars(strip_tags($_POST['contact_info']));
        $address = htmlspecialchars(strip_tags($_POST['address']));
        $address2 = htmlspecialchars(strip_tags($_POST['address2']));
        $photo = $_FILES['photo']['name'];

        // Email validation: Must be @gmail.com
        if(!preg_match("/^[a-zA-Z0-9._%+-]+@gmail\.com$/", $email)) {
            $_SESSION['error'] = 'Email must be a @gmail.com address';
            header('location: profile.php');
            exit();
        }

        // Special character restriction
        $specialChars = "/[<>:\/\$;,?!]/";
        if (preg_match($specialChars, $email) || preg_match($specialChars, $firstname) || preg_match($specialChars, $lastname) || preg_match($specialChars, $address) || preg_match($specialChars, $address2)) {
            $_SESSION['error'] = 'Special characters like <>:/$;,?! are not allowed';
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
