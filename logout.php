<?php
session_start();

session_destroy();
session_start();
$_SESSION['success'] = 'You have been successfully logout.';
header('location: index.php');
exit();
?>