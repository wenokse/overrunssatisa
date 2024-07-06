<?php

$host = "777.37.35.3";
$username = "root";
$password = "1Rootecomm";
$database = "ecomm";

$con = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$con) {
  die("Connection failed: " . mysqli_connect_error());
}

?>