<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: /NEW-PM-JI/pages/admin/");
    exit;
}

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookingId = $_POST['booking_id'];

    // Fetch the current booking details
    $stmt = $pdo->prepare("SELECT status, payment_status FROM tbl_bookings WHERE id = ?");
    $stmt->execute([$bookingId]);
    $booking = $stmt->fetch();

    if (!$booking) {
        $_SESSION['error_message'] = "Booking not found.";
        header("Location: index.php");
        exit;
    }

    // check the current status and payment_status
    if ($booking['status'] === 'pending' && $booking['payment_status'] === 'pending') {
        // approve booking and mark down payment as verified
        $stmt = $pdo->prepare("UPDATE tbl_bookings SET status = 'approved', payment_status = 'down_payment' WHERE id = ?");
        $stmt->execute([$bookingId]);

        $_SESSION['success_message'] = "Booking approved and down payment verified.";
    } elseif ($booking['status'] === 'approved' && $booking['payment_status'] === 'down_payment') {
        // update payment status to fully paid
        $stmt = $pdo->prepare("UPDATE tbl_bookings SET payment_status = 'fully_paid' WHERE id = ?");
        $stmt->execute([$bookingId]);

        $_SESSION['success_message'] = "Payment marked as fully paid.";
    } else {
        $_SESSION['error_message'] = "Invalid booking status or payment status for this action.";
    }

    header("Location: index.php");
    exit;
}
?>