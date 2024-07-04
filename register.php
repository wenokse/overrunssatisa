<?php
	include 'includes/session.php';

	if(isset($_POST['signup'])){
		$firstname = $_POST['firstname'];
		$lastname = $_POST['lastname'];
		$address = $_POST['address'];
		$address2 = $_POST['address2'];
		$contact_info = $_POST['contact_info'];
		$email = $_POST['email'];
		$password = $_POST['password'];
		$repassword = $_POST['repassword'];

		// if(!isset($_SESSION['captcha'])){
		// 	require('recaptcha/src/autoload.php');
		// 	$recaptcha = new \ReCaptcha\ReCaptcha('6LdGIWQfAAAAAMzd7G5PAdIeEhqqZHO-dgBrZeMo', new \ReCaptcha\RequestMethod\SocketPost());
		// 	$resp = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);

		// 	if (!$resp->isSuccess()){
		//   		$_SESSION['error'] = 'Please answer recaptcha correctly';
		//   		header('location: signup.php');
		//   		exit();	
		//   	}	
		//   	else{
		//   		$_SESSION['captcha'] = time() + (10*60);
		//   	}

		// }

		if($password != $repassword){
			$_SESSION['error'] = 'Passwords did not match';
			header('location: signup.php');
		}
		else{
			$conn = $pdo->open();

			$stmt = $conn->prepare("SELECT COUNT(*) AS numrows FROM users WHERE email=:email");
			$stmt->execute(['email'=>$email]);
			$row = $stmt->fetch();

			if($row['numrows'] > 0){
				$_SESSION['error'] = 'Email already taken';
				header('location: signup.php');
			}
			else{
				$now = date('Y-m-d');
				$password = password_hash($password, PASSWORD_DEFAULT);

				try{
					$stmt = $conn->prepare("INSERT INTO users (email, password, firstname, lastname, address, address2, contact_info, status, created_on) VALUES (:email, :password, :firstname, :lastname, :address, :address2, :contact_info, :status, :created_on)");
					$stmt->execute(['email'=>$email, 'password'=>$password, 'firstname'=>$firstname, 'lastname'=>$lastname, 'address'=>$address, 'address2'=>$address2, 'contact_info'=>$contact_info, 'status'=>1, 'created_on'=>$now]);
				    $_SESSION['success'] = 'Account created successfully';
				    header('location: login.php');

				}
				catch (PDOException $e){
					$_SESSION['error'] = $e->getMessage();
					header('location: register.php');
				}

				$pdo->close();
			}

		}

	}
	else{
		$_SESSION['error'] = 'Fill up signup form first';
		header('location: signup.php');
	}

?>