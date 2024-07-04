<?php
	include 'includes/session.php';

	$sale_id = $_GET['sale_id'];

	$conn = $pdo->open();

	$sql = "UPDATE sales SET status=1 WHERE id='$sale_id'";
	$results = $conn->query($sql);

	header('Location: '. $_SERVER['HTTP_REFERER']);
	

?>