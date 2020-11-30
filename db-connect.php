<?php

// eCommerce Employee Portal
// Written by Danish Siddiqui
// Last Updated: 11/27/2020
// db-connect.php


$server = "XXXXXXXXXX";
$user = "XXXXXXXXXX";
$pass = "XXXXXXXXXX";
$dbName = "XXXXXXXXXX";

$conn = mysqli_connect($server, $user, $pass, $dbName);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
