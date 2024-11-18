<?php
session_start();

session_destroy();
session_start();
$_SESSION['success'] = 'Successfully logout.';
header('location: index');
exit();
?>