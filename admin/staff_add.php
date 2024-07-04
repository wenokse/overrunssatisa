<?php
	include 'includes/session.php';

	if(isset($_POST['add'])){
		$firstname = $_POST['firstname'];
		$lastname = $_POST['lastname'];
		$email = $_POST['email'];
		$password = $_POST['password'];
		$repassword = $_POST['repassword'];

		if($password != $repassword){
			$_SESSION['error'] = 'Passwords did not match';
			header('location: staff.php');
		}
		else{
			$conn = $pdo->open();

			$stmt = $conn->prepare("SELECT *, COUNT(*) AS numrows FROM users WHERE email=:email");
			$stmt->execute(['email'=>$email]);
			$row = $stmt->fetch();

			if($row['numrows'] > 0){
				$_SESSION['error'] = 'Email already taken';
			}
			else{
				$password = password_hash($password, PASSWORD_DEFAULT);
				$filename = $_FILES['photo']['name'];
				$now = date('Y-m-d');
				if(!empty($filename)){
					move_uploaded_file($_FILES['photo']['tmp_name'], '../images/'.$filename);	
				}
				try{
					$stmt = $conn->prepare("INSERT INTO users (email, password, type, firstname, lastname, photo, status, created_on) VALUES (:email, :password, :type, :firstname, :lastname, :photo, :status, :created_on)");
					$stmt->execute(['email'=>$email, 'password'=>$password, 'type'=>2, 'firstname'=>$firstname, 'lastname'=>$lastname, 'photo'=>$filename, 'status'=>1, 'created_on'=>$now]);
					$_SESSION['success'] = 'Staff added successfully';

				}
				catch(PDOException $e){
					$_SESSION['error'] = $e->getMessage();
				}
			}

			$pdo->close();
		}
	}
	else{
		$_SESSION['error'] = 'Fill up staff form first';
	}
	
	header('location: staff.php');

?>