<?php
	include 'includes/session.php';

	if(isset($_POST['stockout']) || isset($_POST['addstock'])) {
		$id = $_POST['id'];
		$conn = $pdo->open();

		try {
			if (isset($_POST['addstock']) && is_numeric($_POST['addstock'])) {
				$addStock = intval($_POST['addstock']);
				$stmt = $conn->prepare("SELECT stock FROM products WHERE id=:id");
				$stmt->execute(['id' => $id]);
				$product = $stmt->fetch();
				$currentStock = $product['stock'];
				$newStock = $currentStock + $addStock;
				$stmt = $conn->prepare("UPDATE products SET stock=:stock WHERE id=:id");
				$stmt->execute(['stock' => $newStock, 'id' => $id]);
				$_SESSION['success'] = 'Stock added successfully';
			} else if (isset($_POST['stockout'])) {
				$stmt = $conn->prepare("UPDATE products SET stock=0 WHERE id=:id");
				$stmt->execute(['id' => $id]);
				$_SESSION['success'] = 'Inventory updated successfully';
			}
		}
		catch(PDOException $e) {
			$_SESSION['error'] = $e->getMessage();
		}

		$pdo->close();
	} else {
		$_SESSION['error'] = 'Fill up edit inventory form first';
	}

	header('location: inventory');
?>
