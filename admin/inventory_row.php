<?php 
	include 'includes/session.php';

	if(isset($_POST['id'])){
		$id = $_POST['id'];
		
		$conn = $pdo->open();

		$stmt = $conn->prepare("SELECT *, inventory.id AS inventoryid, inventory.product_name AS prodname, products.name AS prodname FROM inventory LEFT JOIN availability ON availability.name=inventory.availability LEFT JOIN products ON products.name=inventory.product_name WHERE inventory.id=:id");
		$stmt->execute(['id'=>$id]);
		$row = $stmt->fetch();
		
		$pdo->close();

		echo json_encode($row);
	}
?>