<?php
// database.php
$host = 'localhost'; // Hostname
$db = 'admin_website'; // Database name
$user = 'root'; // Database username
$pass = ''; // Database password (default is empty for XAMPP)

$conn = new mysqli($host, $user, $pass, $db);

// Check the connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
