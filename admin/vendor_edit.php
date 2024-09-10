<?php
	include 'includes/session.php';

	if(isset($_POST['edit'])){
		$id = $_POST['id'];
		$firstname = $_POST['firstname'];
		$lastname = $_POST['lastname'];
		$store = $_POST['store'];
		$email = $_POST['email'];
		$password = $_POST['password'];

		$conn = $pdo->open();
		$stmt = $conn->prepare("SELECT * FROM users WHERE id=:id");
		$stmt->execute(['id'=>$id]);
		$row = $stmt->fetch();

		if($password == $row['password']){
			$password = $row['password'];
		}
		else{
			$password = password_hash($password, PASSWORD_DEFAULT);
		}

		try{
			$stmt = $conn->prepare("UPDATE users SET email=:email, password=:password, store=:store, firstname=:firstname, lastname=:lastname WHERE id=:id");
			$stmt->execute(['email'=>$email, 'password'=>$password, 'store'=>$store, 'firstname'=>$firstname, 'lastname'=>$lastname, 'id'=>$id]);
			$_SESSION['success'] = 'Customer updated successfully';

		}
		catch(PDOException $e){
			$_SESSION['error'] = $e->getMessage();
		}
		

		$pdo->close();
	}
	else{
		$_SESSION['error'] = 'Fill up edit customer form first';
	}

	header('location: vendor.php');

?>