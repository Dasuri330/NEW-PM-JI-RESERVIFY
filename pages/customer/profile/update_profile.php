<?php
session_start();

if (!isset($_SESSION['user_email'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

if (!isset($_POST['field']) || !isset($_POST['value'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    exit;
}

// Sanitize input
$field = $_POST['field'];
$value = trim($_POST['value']);

// List of allowed fields to update
$allowed_fields = ['first_name', 'middle_name', 'last_name', 'contact_no', 'email'];
if (!in_array($field, $allowed_fields)) {
    echo json_encode(['status' => 'error', 'message' => 'Field not allowed']);
    exit;
}

// Database connection parameters
$host = 'localhost';
$db = 'db_pmji';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'DB Connection failed']);
    exit;
}

// Update the data in the database
$userEmail = $_SESSION['user_email'];
$sql = "UPDATE tbl_users SET {$field} = :value WHERE email = :email";
$stmt = $pdo->prepare($sql);
try {
    $stmt->execute([':value' => $value, ':email' => $userEmail]);
    echo json_encode(['status' => 'success']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>