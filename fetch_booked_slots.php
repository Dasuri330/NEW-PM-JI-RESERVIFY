<?php
require_once 'database.php';

if (isset($_GET['date'])) {
    $date = $_GET['date'];
    // Ginagamit natin ang alias para maging consistent sa JavaScript
    $sql = "SELECT event_start AS start_time, event_end AS end_time FROM admin_eventcalendar WHERE DATE(event_start) = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $bookedSlots = [];
    while ($row = $result->fetch_assoc()){
        $bookedSlots[] = $row;  // bawat slot may start_time at end_time
    }
    echo json_encode($bookedSlots);
} else {
    echo json_encode([]);
}
?>
