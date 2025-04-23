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

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(htmlspecialchars($_POST['email']));

    // check if the email exists in the database
    $query = "SELECT * FROM tbl_users WHERE LOWER(email) = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$email]);
    $result = $stmt->fetch();

    if ($result) {
        // generate a unique token
        $token = bin2hex(random_bytes(32));
        $expires = date("Y-m-d H:i:s", strtotime("+1 hour"));

        // save the token in the database
        $query = "INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$email, $token, $expires]);

        // send the reset link via email using PHPMailer
        $resetLink = "http://localhost/NEW-PM-JI-RESERVIFY/reset-password.php?token=$token";

        require $_SERVER['DOCUMENT_ROOT'] . '/NEW-PM-JI-RESERVIFY/vendor/autoload.php';

        $mail = new PHPMailer(true);

        try {
            // server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'skypemain01@gmail.com'; // Replace with your email
            $mail->Password = 'nxkt whiw tlft udhl'; // Replace with your app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // recipients
            $mail->setFrom('skypemain01@gmail.com', 'PM&JI Reservify');
            $mail->addAddress($email);

            // content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request - PM&JI Reservify';
            $mail->Body = "
                <h2>Password Reset Request</h2>
                <p>We received a request to reset your password. Click the link below to reset your password:</p>
                <a href='$resetLink'>$resetLink</a>
                <p>If you did not request this, please ignore this email.</p>
            ";

            // send the email
            $mail->send();
            echo 'success';
        } catch (Exception $e) {
            error_log("Email could not be sent. Error: {$mail->ErrorInfo}");
            echo 'Error sending email.';
        }
    } else {
        echo 'Email not found!';
    }
}
?>