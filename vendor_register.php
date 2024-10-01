<?php
	include 'includes/session.php';

	if(isset($_POST['signup'])){
		// Sanitize other fields
		$store = htmlspecialchars($_POST['store']);
		$firstname = htmlspecialchars($_POST['firstname']);
		$lastname = htmlspecialchars($_POST['lastname']);
		$address = htmlspecialchars($_POST['address']);
		$address2 = htmlspecialchars($_POST['address2']);
		$contact_info = htmlspecialchars($_POST['contact_info']);
		$email = htmlspecialchars($_POST['email']);
		
		// Password fields do not require htmlspecialchars but should be securely hashed
		$password = $_POST['password'];
		$repassword = $_POST['repassword'];
		
		$photo = htmlspecialchars($_POST['photo']);
		$business_permit = htmlspecialchars($_POST['business_permit']);

		// Special character validation for store and name fields
		$invalid_chars = "/[<>:\/$;,?!]/";

		if (preg_match($invalid_chars, $store) || preg_match($invalid_chars, $firstname) || preg_match($invalid_chars, $lastname)) {
			$_SESSION['error'] = 'Special characters like <>:/$;,?! are not allowed.';
			header('location: vendor_signup.php');
		}
		// Ensure email is Gmail
		else if (!preg_match("/^[a-zA-Z0-9._%+-]+@gmail\.com$/", $email)) {
			$_SESSION['error'] = 'Email must be a Gmail account (@gmail.com)';
			header('location: vendor_signup.php');
		}
		// Check if passwords match
		else if ($password != $repassword) {
			$_SESSION['error'] = 'Passwords did not match';
			header('location: vendor_signup.php');
		}
		else {
			$conn = $pdo->open();
			$stmt = $conn->prepare("SELECT COUNT(*) AS numrows FROM users WHERE email=:email");
			$stmt->execute(['email'=>$email]);
			$row = $stmt->fetch();

			if($row['numrows'] > 0){
				$_SESSION['error'] = 'Email already taken';
				header('location: vendor_signup.php');
			}
			else{
				$now = date('Y-m-d');
				
				// Hash the password before storing it
				$password = password_hash($password, PASSWORD_DEFAULT);

				try{
					$stmt = $conn->prepare("INSERT INTO users (email, password, firstname, lastname, store, address, address2, contact_info, photo, business_permit, type, status, created_on) VALUES (:email, :password, :firstname, :lastname, :store, :address, :address2, :contact_info, :photo, :business_permit, :type, :status, :created_on)");
					$stmt->execute(['email'=>$email, 'password'=>$password, 'firstname'=>$firstname, 'lastname'=>$lastname, 'store'=>$store, 'address'=>$address, 'address2'=>$address2, 'contact_info'=>$contact_info, 'photo'=>$photo, 'business_permit'=>$business_permit, 'type'=>2, 'status'=>3, 'created_on'=>$now]);
					$_SESSION['success'] = 'Account created successfully, waiting for admin approval.';
					header('location: login.php');
				}
				catch (PDOException $e){
					$_SESSION['error'] = $e->getMessage();
					header('location: vendor_register.php');
				}

				$pdo->close();
			}
		}
	}
	else{
		$_SESSION['error'] = 'Fill up signup form first';
		header('location: vendor_signup.php');
	}
?>
