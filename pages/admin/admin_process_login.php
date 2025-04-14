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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and trim admin username and password
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Ensure both fields are provided
    if (!$username || !$password) {
        echo 'failure';
        exit();
    }

    // Prepare query to select the admin record by username.
    $sql = "SELECT * FROM tbl_admin WHERE username = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':username' => $username]);
    $adminData = $stmt->fetch();

    // Verify that the admin exists and the password is correct.
    if ($adminData && password_verify($password, $adminData['password'])) {
        if (!$adminData['is_active']) {
            echo 'inactive';
            exit();
        }
        // Set session variables for the admin account.
        $_SESSION['admin_id'] = $adminData['id'];
        $_SESSION['admin_username'] = $adminData['username'];
        
        header('Location: dashboard/admin_dashboard.php');
        exit();
    } else {
        echo password_hash('temporaryPass', PASSWORD_DEFAULT);
        exit();
    }
}
?>
