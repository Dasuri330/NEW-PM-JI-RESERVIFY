<?php
require_once "database.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reservation_id = $_POST["reservation_id"];
    $status = $_POST["status"];

    if ($reservation_id && $status) {
        $sql = "UPDATE reservation SET status = ? WHERE reservation_id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("si", $status, $reservation_id);
            if ($stmt->execute()) {
                echo "success";
            } else {
                echo "error";
            }
            $stmt->close();
        } else {
            echo "error";
        }
    } else {
        echo "invalid";
    }
    $conn->close();
}
?>
