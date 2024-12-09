<?php
	include 'includes/session.php';

	if(isset($_GET['return'])){
		$return = $_GET['return'];
	} else {
		$return = 'home';
	}

	if(isset($_POST['save'])){
		$curr_password = $_POST['curr_password'];
		$email = $_POST['email'];
		$password = $_POST['password'];
		$firstname = $_POST['firstname'];
		$lastname = $_POST['lastname'];
		$contact_info = $_POST['contact_info'];
		$photo = $_FILES['photo']['name'];
		$allowed_extensions = ['jpg', 'jpeg', 'png']; // Allowed extensions

		// Get the file extension
		$file_ext = strtolower(pathinfo($photo, PATHINFO_EXTENSION));

		if(password_verify($curr_password, $admin['password'])){
			// Check if a photo was uploaded
			if(!empty($photo)){
				// Validate file type
				if(in_array($file_ext, $allowed_extensions)){
					move_uploaded_file($_FILES['photo']['tmp_name'], '../images/'.$photo);
					$filename = $photo;	
				} else {
					$_SESSION['error'] = 'Invalid file type. Only PNG, JPG, and JPEG files are allowed.';
					header('location:'.$return);
					exit();
				}
			} else {
				$filename = $admin['photo'];
			}

			// Check if password was changed
			if($password == $admin['password']){
				$password = $admin['password'];
			} else {
				$password = password_hash($password, PASSWORD_DEFAULT);
			}

			$conn = $pdo->open();

			try{
				$stmt = $conn->prepare("UPDATE users SET email=:email, password=:password, firstname=:firstname, lastname=:lastname, contact_info=:contact_info, photo=:photo, updated_on=NOW() WHERE id=:id");
				$stmt->execute(['email'=>$email, 'password'=>$password, 'firstname'=>$firstname, 'lastname'=>$lastname, 'contact_info'=>$contact_info, 'photo'=>$filename, 'id'=>$admin['id']]);

				$_SESSION['success'] = 'Account updated successfully';
			}
			catch(PDOException $e){
				$_SESSION['error'] = $e->getMessage();
			}

			$pdo->close();
		} else {
			$_SESSION['error'] = 'Incorrect password';
		}
	} else {
		$_SESSION['error'] = 'Fill up required details first';
	}

	header('location:'.$return);
?>
