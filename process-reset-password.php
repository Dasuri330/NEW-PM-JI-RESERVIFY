<?php
session_start();

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

$token = htmlspecialchars($_POST['token'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Validate the token (expiration date check removed)
    $query = "SELECT * FROM password_resets WHERE token = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$token]);
    $result = $stmt->fetch();

    if ($result) {
        $email = $result['email'];

        // Update the user's password
        $query = "UPDATE tbl_users SET password = ? WHERE email = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$password, $email]);

        if ($stmt->rowCount() > 0) {
            // Delete the token
            $query = "DELETE FROM password_resets WHERE token = ?";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$token]);

            // Log the user in by creating a session
            $_SESSION['user_email'] = $email;

            // Redirect to the homepage
            header('Location: /NEW-PM-JI-RESERVIFY/pages/customer/home.php');
            exit();
        } else {
            // Failed to update password
            header('Location: /NEW-PM-JI-RESERVIFY/reset-password-error.php');
            exit();
        }
    } else {
        // Invalid or missing token
        header('Location: /NEW-PM-JI-RESERVIFY/reset-password-error.php');
        exit();
    }
}
?>