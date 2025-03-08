<?php
require 'database.php'; // Siguraduhin na tama ang connection file mo

if (isset($_GET['reservation_id'])) {
    $reservation_id = intval($_GET['reservation_id']);

    $query = $conn->prepare("SELECT * FROM reservation WHERE id = ?");
    $query->bind_param("i", $reservation_id);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $reservation = $result->fetch_assoc();
        echo json_encode([
            "success" => true,
            "reservation_id" => $reservation['id'],
            "name" => $reservation['name'],
            "email" => $reservation['email'],
            "event_type" => $reservation['event_type'],
            "others" => $reservation['others'],
            "event_place" => $reservation['event_place'],
            "photo_size_layout" => $reservation['photo_size_layout'],
            "contact_number" => $reservation['contact_number'],
            "start_time" => $reservation['start_time'],
            "end_time" => $reservation['end_time'],
            "status" => $reservation['status'],
            "image_url" => $reservation['image_url']
        ]);
    } else {
        echo json_encode(["success" => false, "error" => "Reservation ID not found in database."]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Reservation ID not provided."]);
}
?>
