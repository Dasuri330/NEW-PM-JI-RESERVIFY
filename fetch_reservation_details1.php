<?php
require_once "database.php";

if (!isset($_GET['reservation_id'])) {
    echo json_encode(["success" => false, "error" => "Reservation ID missing"]);
    exit;
}

$reservation_id = $_GET['reservation_id'];

// ✅ Fetch Reservation Data
$stmt = $conn->prepare("SELECT * FROM reservation WHERE id = ?");
$stmt->bind_param("i", $reservation_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode([
        "success" => true,
        "reservation_id" => $row['id'],
        "name" => $row['name'],
        "email" => $row['email'],
        "event_type" => $row['event_type'],
        "others" => $row['others'],
        "event_place" => $row['event_place'],
        "photo_size_layout" => $row['photo_size_layout'],
        "contact_number" => $row['contact_number'],
        "start_time" => $row['start_time'],
        "end_time" => $row['end_time'],
        "status" => $row['status'],
        "image_url" => $row['image_url']
    ]);
} else {
    echo json_encode(["success" => false, "error" => "Reservation not found"]);
}
?>
