<?php
session_start();
require_once "database.php";

// Kunin ang raw JSON data mula sa POST request
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'No data received']);
    exit();
}

// Kunin ang bawat field mula sa JSON data
$customer_name     = isset($data['customer_name']) ? $data['customer_name'] : '';
$event_type        = isset($data['event_type']) ? $data['event_type'] : '';
$event_place       = isset($data['event_place']) ? $data['event_place'] : '';
$photo_size_layout = isset($data['photo_size_layout']) ? $data['photo_size_layout'] : '';
$contact_number    = isset($data['contact_number']) ? $data['contact_number'] : '';
$event_start       = isset($data['event_start']) ? $data['event_start'] : '';
$event_end         = isset($data['event_end']) ? $data['event_end'] : '';

// Ihanda ang SQL query para i-save ang event data
$sql = "INSERT INTO admin_eventcalendar (customer_name, event_type, event_place, photo_size_layout, contact_number, event_start, event_end) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => $conn->error]);
    exit();
}

$stmt->bind_param("sssssss", $customer_name, $event_type, $event_place, $photo_size_layout, $contact_number, $event_start, $event_end);

// I-execute ang query at i-check kung matagumpay
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Event saved successfully!']);
} else {
    echo json_encode(['success' => false, 'message' => $stmt->error]);
}

$stmt->close();
?>
