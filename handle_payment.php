<?php 
// ITONG CODEBASE FILE NA ITO AY PARA SA PAYMENT ACTION (Approve/Reject)
require_once "database.php";
require 'vendor/autoload.php'; // PHPMailer autoload

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Function to insert notification sa customer_notifications table
function insertNotification($user_id, $reservation_id, $message, $reject_reason = null) {
    global $conn;
    $sql = "INSERT INTO customer_notifications (user_id, reservation_id, message, reject_reason, status, created_at, updated_at) 
            VALUES (?, ?, ?, ?, 'unread', NOW(), NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiss", $user_id, $reservation_id, $message, $reject_reason);
    
    if (!$stmt->execute()) {
        echo "<script>alert('Error inserting notification: " . $stmt->error . "');</script>";
    }
}

// Function para makuha ang user_id at reservation_id mula sa payment table
function getPaymentDetails($payment_id) {
    global $conn;
    $sql = "SELECT user_id, reservation_id FROM payment WHERE payment_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $payment_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Function para kunin ang customer details (Email at Full Name) mula sa user table
function getCustomerDetails($user_id) {
    global $conn;
    // Adjust ang query ayon sa iyong schema; dito ginagamit natin ang test_registration table bilang halimbawa.
    $sql = "SELECT Email, CONCAT(First_Name, ' ', Last_Name) AS fullName FROM test_registration WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Function para i-update ang payment status at magpadala ng email notification sa customer
function updatePaymentStatus($payment_id, $status, $reject_reason = null) {
    global $conn;
    
    // Kuhanin ang payment details
    $paymentDetails = getPaymentDetails($payment_id);
    if (!$paymentDetails) {
        echo "<script>alert('Error: Payment details not found.'); window.history.back();</script>";
        exit;
    }
    $user_id = $paymentDetails['user_id'];
    $reservation_id = $paymentDetails['reservation_id'];
    
    // Update payment status (hindi muna isama ang reject_reason dito)
    $sql = "UPDATE payment SET status = ? WHERE payment_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $payment_id);
    
    if ($stmt->execute()) {
        // I-set ang message base sa status
        if ($status == "approved") {
            $message = "Your payment has been approved! You now proceed to your <a href='Booking Summary.php' style='text-decoration: underline; color: blue;'>Booking Summary</a>.";
            $reject_reason = null; // Walang reject reason kung approved
        } else {
            $message = "Your payment has been rejected. Reason: " . $reject_reason;
        }
        
        // I-insert ang notification sa database
        insertNotification($user_id, $reservation_id, $message, $reject_reason);

        // Kunin ang customer details para sa email
        $customerDetails = getCustomerDetails($user_id);
        $customerEmail = $customerDetails['Email'] ?? "";
        $customerName  = $customerDetails['fullName'] ?? "Customer";

        // Magpadala ng email notification sa customer kung may email available
        if (!empty($customerEmail)) {
            $mail = new PHPMailer(true);
            try {
                // SMTP configuration
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'reservifypmji@gmail.com'; // Fixed sender email
                $mail->Password   = 'zxjs nprh gvrn dkny'; // Palitan ng tamang App Password o password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;
    
                // Set sender at recipient
                $mail->setFrom('reservifypmji@gmail.com', 'PM&JI Reservify Notifications');
                $mail->addAddress($customerEmail, $customerName);
    
                // I-set ang subject at body depende sa status
                if ($status === 'approved') {
                    $mail->Subject = 'Payment Approved';
                    $mail->Body    = "
                        <p>Dear " . htmlspecialchars($customerName) . ",</p>
                        <p>Your payment has been approved! Please log in to your account to view your Booking Summary and take further action if needed.</p>
                        <p>Thank you,<br>PM&JI Reservify</p>
                    ";
                } elseif ($status === 'rejected') {
                    $mail->Subject = 'Payment Rejected';
                    $mail->Body    = "
                        <p>Dear " . htmlspecialchars($customerName) . ",</p>
                        <p>Your payment has been rejected. Reason: " . htmlspecialchars($reject_reason) . ".</p>
                        <p>Please contact us for further assistance.</p>
                        <p>Thank you,<br>PM&JI Reservify</p>
                    ";
                }
    
                $mail->isHTML(true);
                $mail->send();
            } catch (Exception $e) {
                // Optional: I-log ang error kung hindi naipadala ang email
                error_log("Email could not be sent. PHPMailer Error: {$mail->ErrorInfo}");
            }
        }
    
        echo "<script>alert('Payment has been " . ($status == 'approved' ? 'approved' : 'rejected') . ".'); window.location.href = 'admin_payments1.php';</script>";
    } else {
        echo "<script>alert('Error updating payment: " . $stmt->error . "'); window.history.back();</script>";
    }
}

// Handle Approve/Reject Request mula sa admin panel
if (isset($_POST['action']) && isset($_POST['payment_id'])) {
    $payment_id = $_POST['payment_id'];
    $action = $_POST['action'];
    
    if ($action == "approve") {
        updatePaymentStatus($payment_id, "approved");
    } elseif ($action == "reject") {
        $reject_reason = $_POST['reject_reason'] ?? "No reason provided.";
        updatePaymentStatus($payment_id, "rejected", $reject_reason);
    }
}
?>
