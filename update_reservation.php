<?php
require_once "database.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $reservation_id = $_POST['reservation_id'] ?? null;
    $status = $_POST['status'] ?? null;

    if ($reservation_id && ($status === "approved" || $status === "rejected")) {
        // Check if the reservation ID exists before updating
        $check_sql = "SELECT * FROM reservation WHERE reservation_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("i", $reservation_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();

        if ($result->num_rows > 0) {
            // Proceed with the update
            $sql = "UPDATE reservation SET status = ? WHERE reservation_id = ?";
            $stmt = $conn->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("si", $status, $reservation_id);
                if ($stmt->execute()) {
                    if ($stmt->affected_rows > 0) {
                        echo "Reservation status updated successfully.";
                    } else {
                        echo "Reservation status is already '$status'. No changes made.";
                    }
                } else {
                    echo "Execute failed: " . $stmt->error;
                }
                $stmt->close();
            } else {
                echo "Prepare failed: " . $conn->error;
            }
        } else {
            echo "Error: Reservation ID does not exist.";
        }

        $check_stmt->close();
    } else {
        echo "Invalid request. Missing reservation ID or incorrect status.";
    }

    $conn->close();
}
?>
