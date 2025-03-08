<?php
// Include your database connection file
require_once "database.php";
require 'vendor/autoload.php'; // PHPMailer autoload

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Function to insert notification into `customer_notifications`
function insertNotification($user_id, $reservation_id, $message, $reject_reason = null) {
    global $conn;

    // Check if the same notification already exists
    $check_sql = "SELECT id FROM customer_notifications WHERE user_id = ? AND reservation_id = ? AND message = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("iis", $user_id, $reservation_id, $message);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        return; // If notification already exists, do not insert again
    }

    // Insert new notification
    $sql = "INSERT INTO customer_notifications (user_id, reservation_id, message, reject_reason, status, created_at, updated_at) 
            VALUES (?, ?, ?, ?, 'unread', NOW(), NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiss", $user_id, $reservation_id, $message, $reject_reason);
    
    if (!$stmt->execute()) {
        echo "<script>alert('Error inserting notification: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}

// Function to get user_id from reservations table
function getUserIdFromReservation($reservation_id, $conn) {
    $sql = "SELECT user_id FROM reservation WHERE reservation_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $reservation_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $stmt->close();
        return $row['user_id']; // Return user_id if found
    }
    $stmt->close();
    return null; // Return null if not found
}

// Function to check if the reservation is already processed
function isAlreadyProcessed($reservation_id, $conn) {
    $sql = "SELECT status FROM reservation WHERE reservation_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $reservation_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $stmt->close();
        return $row['status']; // Return current status
    }
    $stmt->close();
    return null;
}

// Function to get customer details (Email and full name) from test_registration table
function getCustomerDetails($user_id, $conn) {
    $sql = "SELECT Email, CONCAT(First_Name, ' ', Last_Name) AS fullName FROM test_registration WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $details = $result->fetch_assoc();
    $stmt->close();
    return $details;
}

// Check if action and reservation_id are set
if (isset($_POST['action']) && isset($_POST['reservation_id'])) {
    $reservation_id = $_POST['reservation_id'];
    $action = $_POST['action'];

    // Get user_id from the reservations table
    $user_id = getUserIdFromReservation($reservation_id, $conn);

    if ($user_id === null) {
        echo "<script>alert('Error: User not found for this reservation.'); window.history.back();</script>";
        exit;
    }

    // Check if the reservation has already been processed
    $current_status = isAlreadyProcessed($reservation_id, $conn);
    
    if ($current_status == 'approved' && $action == 'approve') {
        echo "<script>alert('This reservation is already approved.'); window.history.back();</script>";
        exit;
    }

    if ($current_status == 'rejected' && $action == 'reject') {
        echo "<script>alert('This reservation is already rejected.'); window.history.back();</script>";
        exit;
    }

    if ($action == 'approve') {
        // Update status in reservation table
        $sql = "UPDATE reservation SET status = 'approved' WHERE reservation_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $reservation_id);

        if ($stmt->execute()) {
            // Insert notification for the customer
            $message = "Thank you for choosing PM&JI Pictures!. Your reservation has been approved! You can now proceed to <a href='payment.php' style='text-decoration: underline; color: blue;'>payment</a>.";
            insertNotification($user_id, $reservation_id, $message);

            // Get customer details for email
            $customerDetails = getCustomerDetails($user_id, $conn);
            $customerEmail = $customerDetails['Email'] ?? "";
            $customerName  = $customerDetails['fullName'] ?? "Customer";

            // PHPMailer code to send email notification to customer
            if (!empty($customerEmail)) {
                $mail = new PHPMailer(true);
                try {
                    // SMTP configuration
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'reservifypmji@gmail.com'; // Sender email
                    $mail->Password   = 'zxjs nprh gvrn dkny';    // Replace with correct App Password or password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = 587;
        
                    // Set sender and recipient
                    $mail->setFrom('reservifypmji@gmail.com', 'PM&JI Reservify Notifications');
                    $mail->addAddress($customerEmail, $customerName);
        
                    // Email content for approval
                    $mail->isHTML(true);
                    $mail->Subject = 'Reservation Approved';
                    $mail->Body    = "
                        <p>Dear " . htmlspecialchars($customerName) . ",</p>
                        <p>Your reservation has been approved!</p>
                        <p>Please log in to your account to view further details and proceed to payment.</p>
                        <p>Thank you,<br>PM&JI Reservify</p>
                    ";
        
                    $mail->send();
                } catch (Exception $e) {
                    error_log("Email could not be sent. PHPMailer Error: " . $mail->ErrorInfo);
                }
            }
        
            echo "<script>alert('Reservation Approved.'); window.location.href = 'admin_bookings.php';</script>";
        } else {
            echo "<script>alert('Error updating reservation: " . $stmt->error . "'); window.history.back();</script>";
        }
        $stmt->close();
    } elseif ($action == 'reject') {
        $reject_reason = $_POST['reject_reason'];
        
        // Update status in reservation table
        $sql = "UPDATE reservation SET status = 'rejected' WHERE reservation_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $reservation_id);

        if ($stmt->execute()) {
            // Insert rejection notification
            $message = "Your reservation has been rejected. Reason: " . $reject_reason;
            insertNotification($user_id, $reservation_id, $message, $reject_reason);

            // Get customer details for email
            $customerDetails = getCustomerDetails($user_id, $conn);
            $customerEmail = $customerDetails['Email'] ?? "";
            $customerName  = $customerDetails['fullName'] ?? "Customer";

            // PHPMailer code to send rejection email to customer
            if (!empty($customerEmail)) {
                $mail = new PHPMailer(true);
                try {
                    // SMTP configuration
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'reservifypmji@gmail.com';
                    $mail->Password   = 'zxjs nprh gvrn dkny';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = 587;
        
                    // Set sender and recipient
                    $mail->setFrom('reservifypmji@gmail.com', 'PM&JI Reservify Notifications');
                    $mail->addAddress($customerEmail, $customerName);
        
                    // Email content for rejection
                    $mail->isHTML(true);
                    $mail->Subject = 'Reservation Rejected';
                    $mail->Body    = "
                        <p>Dear " . htmlspecialchars($customerName) . ",</p>
                        <p>Your reservation has been rejected.</p>
                        <p><strong>Reason:</strong> " . htmlspecialchars($reject_reason) . "</p>
                        <p>Please contact us for further assistance.</p>
                        <p>Thank you,<br>PM&JI Reservify</p>
                    ";
        
                    $mail->send();
                } catch (Exception $e) {
                    error_log("Email could not be sent. PHPMailer Error: " . $mail->ErrorInfo);
                }
            }
        
            echo "<script>alert('Reservation Rejected.'); window.location.href = 'admin_bookings.php';</script>";
        } else {
            echo "<script>alert('Error updating reservation: " . $stmt->error . "'); window.history.back();</script>";
        }
        $stmt->close();
    }
}
?>
