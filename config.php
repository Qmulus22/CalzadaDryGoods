<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "calzada_dry_goods";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    // Log error to a file instead of displaying it (for production environments)
    error_log("Connection failed: " . $conn->connect_error);
    // Display a generic error message to users (optional)
    die("Sorry, there was a problem connecting to the database.");
}

// Set charset to UTF-8 for better compatibility with special characters
$conn->set_charset("utf8");
?>
