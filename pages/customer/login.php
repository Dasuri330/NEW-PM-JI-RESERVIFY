<?php
session_start();

$host = 'localhost';
$db   = 'db_pmji';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var(trim($_POST['Email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $password = trim($_POST['Password'] ?? '');

    if (!$email || !$password) {
        echo 'failure';
        exit();
    }

    $sql = "SELECT * FROM tbl_users WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':email' => $email]);
    $userData = $stmt->fetch();

    if ($userData && password_verify($password, $userData['password'])) {
        if (!$userData['is_verified']) {
            echo 'unverified';
            exit();
        }
        // Set session variables for use in home.php
        $_SESSION['id'] = $userData['id'];
        $_SESSION['user_email'] = $userData['email'];
        $_SESSION['first_name'] = $userData['first_name'] ?? '';
        echo 'success';
        exit();
    } else {
        echo 'failure';
        exit();
    }
}
?>
