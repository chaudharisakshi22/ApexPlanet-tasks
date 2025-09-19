<?php
$host = "localhost";
$user = "root";
$pass = ""; // set to your MySQL root password
$dbname = "blog";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

session_start();
?>