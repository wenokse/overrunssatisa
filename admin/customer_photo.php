<?php
	include 'includes/session.php';

	if(isset($_POST['upload'])){
		$id = $_POST['id'];
		$filename = $_FILES['photo']['name'];
		$allowed_extensions = ['jpg', 'jpeg', 'png']; // Allowed file extensions

		// Get the file extension
		$file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

		if(!empty($filename)){
			// Validate file type
			if(in_array($file_ext, $allowed_extensions)){
				move_uploaded_file($_FILES['photo']['tmp_name'], '../images/'.$filename);	
			} else {
				$_SESSION['error'] = 'Invalid file type. Only PNG, JPG, and JPEG files are allowed.';
				header('location: customer');
				exit();
			}
		} else {
			$_SESSION['error'] = 'No file uploaded.';
			header('location: customer');
			exit();
		}
		
		$conn = $pdo->open();

		try{
			$stmt = $conn->prepare("UPDATE users SET photo=:photo WHERE id=:id");
			$stmt->execute(['photo'=>$filename, 'id'=>$id]);
			$_SESSION['success'] = 'Customer photo updated successfully';
		}
		catch(PDOException $e){
			$_SESSION['error'] = $e->getMessage();
		}

		$pdo->close();

	} else {
		$_SESSION['error'] = 'Select customer to update photo first';
	}

	header('location: customer');
?>
