<?php
// Database configuration
$host = 'localhost';
$username = 'root';  // default XAMPP username
$password = '';      // default XAMPP password
$database = 'login_system';

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8mb4
$conn->set_charset("utf8mb4");
?> 