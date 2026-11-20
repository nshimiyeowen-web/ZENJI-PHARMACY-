<?php
// includes/connect.php

$sn = "localhost";
$un = "root";
$pass = "";
$db = "zenji_pharmacy";

// Create connection using MySQLi
$conn = mysqli_connect($sn, $un, $pass, $db);

// Check connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Set charset to utf8mb4 for proper text handling
mysqli_set_charset($conn, "utf8mb4");
?>