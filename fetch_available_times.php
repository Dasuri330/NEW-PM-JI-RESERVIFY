<?php
header('Content-Type: application/json');
 require_once "database.php";

// Start time at 9:00 AM and end time at 5:30 PM
$startTime = strtotime("09:00 AM");
$endTime = strtotime("05:30 PM");

$availableTimes = [];

// Generate 1-hour interval time slots
while ($startTime < $endTime) {
    $nextTime = strtotime("+1 hour", $startTime);

    // Ensure the last slot ends at 5:30 PM
    if ($nextTime > $endTime) {
        $nextTime = $endTime;
    }

    $availableTimes[] = date("h:i A", $startTime) . " - " . date("h:i A", $nextTime);

    // Stop if the next time is 5:30 PM
    if ($nextTime == $endTime) {
        break;
    }

    $startTime = $nextTime;
}

echo json_encode($availableTimes);
?>
