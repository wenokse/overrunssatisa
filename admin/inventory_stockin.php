<?php
	include 'includes/session.php';

	if(isset($_POST['stockin'])){
		$id = $_POST['id'];

		$conn = $pdo->open();

		try{
			$stmt = $conn->prepare("UPDATE products SET avail=:avail WHERE id=:id");
			$stmt->execute(['avail'=>1, 'id'=>$id]);
			$_SESSION['success'] = 'Inventory updated successfully';
		}
		catch(PDOException $e){
			$_SESSION['error'] = $e->getMessage();
		}
		
		$pdo->close();
	}
	else{
		$_SESSION['error'] = 'Fill up edit inventory form first';
	}

	header('location: inventory');

?>