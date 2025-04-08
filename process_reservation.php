<?php
session_start();
if (!isset($_SESSION['user_email'])) {
  header("Location: index.php");
  exit();
}

require 'db_connection.php'; // adjust to your actual DB connection

$email = $_SESSION['user_email'];
$date = $_POST['reservation_date'];
$time = $_POST['time_slot'];
$street = $_POST['street_address'];
$barangay = $_POST['barangay'];
$city = $_POST['city'];
$province = $_POST['province'];

$sql = "INSERT INTO reservations (user_email, reservation_date, time_slot, street, barangay, city, province)
        VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssss", $email, $date, $time, $street, $barangay, $city, $province);

if ($stmt->execute()) {
  $_SESSION['reservation_success'] = "Reservation submitted successfully!";
} else {
  $_SESSION['reservation_error'] = "Something went wrong. Try again.";
}

header("Location: home.php"); // Redirect back to home
exit();
?>
