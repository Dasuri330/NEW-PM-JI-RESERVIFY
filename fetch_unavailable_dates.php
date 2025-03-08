<?php
header('Content-Type: application/json');
require_once 'database.php';

// Query para kunin ang start_time at end_time mula sa admin_eventcalendar
$query = "SELECT start_time, end_time FROM admin_eventcalendar";
$result = mysqli_query($conn, $query);

$timeSlots = array();

if ($result) {
    while($row = mysqli_fetch_assoc($result)) {
        $timeSlots[] = array(
            'start_time' => $row['start_time'],
            'end_time'   => $row['end_time']
        );
    }
    echo json_encode($timeSlots);
} else {
    echo json_encode(['error' => 'Database query failed.']);
}

mysqli_close($conn);
?>
