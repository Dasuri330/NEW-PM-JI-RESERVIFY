<?php
// Include your database connection file
require_once "database.php";

// Function to get user_id from reservations table
function getUserIdFromReservation($reservation_id, $conn) {
    $sql = "SELECT user_id FROM reservation WHERE reservation_id = ?"; // Dito kukunin ang user_id
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $reservation_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        return $row['user_id']; // Ibalik ang user_id kung meron
    }
    return null; // Kung walang nahanap
}

// Check if the action and reservation_id are set
if (isset($_POST['action']) && isset($_POST['reservation_id'])) {
    $reservation_id = $_POST['reservation_id'];
    $action = $_POST['action'];

    // Kunin ang user_id mula sa `reservations` table
    $user_id = getUserIdFromReservation($reservation_id, $conn);

    if ($user_id === null) {
        echo "<script>alert('Error: User not found for this reservation.'); window.history.back();</script>";
        exit;
    }

    if ($action == 'approve') {
        // Update status sa reservations table
        $sql = "UPDATE reservation SET status = 'approved' WHERE reservation_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $reservation_id);

        if ($stmt->execute()) {
            // Maglagay ng notification sa customer_notifications table
            $message = "Your reservation has been approved.";
            insertNotification($user_id, $message);

            // Alert message para sa success
            echo "<script>alert('Reservation Approved.'); window.location.href = 'admin_bookings.php';</script>";
        } else {
            echo "<script>alert('Error updating reservation: " . $stmt->error . "'); window.history.back();</script>";
        }
    } elseif ($action == 'reject') {
        $reject_reason = $_POST['reject_reason'];

        // Update status sa reservations table
        $sql = "UPDATE reservation SET status = 'rejected', reject_reason = ? WHERE reservation_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $reject_reason, $reservation_id);

        if ($stmt->execute()) {
            // Maglagay ng notification sa customer_notifications table
            $message = "Your reservation has been rejected. Reason: " . $reject_reason;
            insertNotification($user_id, $message);

            // Alert message para sa success
            echo "<script>alert('Reservation Rejected.'); window.location.href = 'admin_bookings.php';</script>";
        } else {
            echo "<script>alert('Error updating reservation: " . $stmt->error . "'); window.history.back();</script>";
        }
    }
}

// Function para maglagay ng notification sa `customer_notifications`
function insertNotification($user_id, $message) {
    global $conn;
    $sql = "INSERT INTO customer_notifications (user_id, message, status, created_at, updated_at) 
            VALUES (?, ?, 'unread', NOW(), NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $user_id, $message);
    if (!$stmt->execute()) {
        echo "<script>alert('Error inserting notification: " . $stmt->error . "');</script>";
    }
}
?>
