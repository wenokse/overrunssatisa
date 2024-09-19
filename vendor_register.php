<?php
	include 'includes/session.php';

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
		$photo = $_POST['photo'];
		$business_permit = $_POST['business_permit'];


		if($password != $repassword){
			$_SESSION['error'] = 'Passwords did not match';
			header('location: vendor_signup.php');
		}
		else{
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