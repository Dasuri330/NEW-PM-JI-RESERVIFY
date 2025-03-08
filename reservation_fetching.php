<?php
session_start();
include 'db_connection.php'; // Ensure this includes database connection

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id']; // Assuming user_id is stored in session

$query = "SELECT 
            cn.message, 
            r.event_type, r.others, r.event_place, r.photo_size_layout, 
            r.start_time, r.end_time, r.status, 
            tr.Email, tr.Phone_Number
          FROM reservation r
          JOIN customer_notification cn ON r.id = cn.reservation_id
          JOIN test_registration tr ON r.customer_id = tr.id
          WHERE r.customer_id = ? 
          ORDER BY r.start_time DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$bookings = [];
while ($row = $result->fetch_assoc()) {
    $bookings[] = $row;
}

echo json_encode($bookings);
?>
