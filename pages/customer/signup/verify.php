<?php
// verify.php

$host = 'localhost';
$db = 'db_pmji';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// Get email and token from the query string
$email = isset($_GET['email']) ? trim($_GET['email']) : '';
$token = isset($_GET['token']) ? trim($_GET['token']) : '';

if (!$email || !$token) {
    die('Invalid verification link.');
}

// Check if user with matching email and token exists and is not yet verified
$sql = "SELECT * FROM tbl_users WHERE email = :email AND verification_token = :token AND is_verified = 0";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':email' => $email,
    ':token' => $token,
]);
$user = $stmt->fetch();

if ($user) {
    // Update user to set is_verified = 1
    $updateSql = "UPDATE tbl_users SET is_verified = 1, verification_token = NULL WHERE email = :email";
    $updateStmt = $pdo->prepare($updateSql);
    $updateStmt->execute([':email' => $email]);

    // Redirect to home.php after successful verification
    header("Location: /NEW-PM-JI/pages/customer/home.php?verified=1");
    exit;
} else {
    echo "⚠️ Invalid or expired verification link, or your account is already verified.";
}
?>
