<?php
// Database connection settings
$host = 'localhost';
$username = 'root'; // Replace with your database username
$password = 'ADF-01 FALKEN F';     // Replace with your database password
$dbname = 'amu';    // Replace with your database name

// Create a connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Debugging: Confirm connection success
// Uncomment the line below to test the connection
// echo "Database connected successfully!";
?>