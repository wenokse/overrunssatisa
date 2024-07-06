<?php

$host = "127.0.0.1:3306";
$username = "u510162695_root";
$password = "1RootEcomm";
$database = "u510162695_ecomm";

$con = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$con) {
  die("Connection failed: " . mysqli_connect_error());
}

?>