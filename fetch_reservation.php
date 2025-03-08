<?php
session_start();
require_once "database.php";

$user_id = $_SESSION['id'] ?? null;

if (!$user_id) {
    echo json_encode(["error" => "You must be logged in."]);
    exit();
}

$query = "SELECT * FROM reservation WHERE user_id = ? ORDER BY reservation_id DESC";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$reservations = [];

while ($reservation = mysqli_fetch_assoc($result)) {
    $reservations[] = $reservation;
}

echo json_encode($reservations);

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
