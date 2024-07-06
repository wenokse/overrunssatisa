<?php

$host = "77.37.35.3";
$username = "u510162695_root";
$password = "1RootEcomm";
$database = "u510162695_ecomm";

$con = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$con) {
  die("Connection failed: " . mysqli_connect_error());
}

?>