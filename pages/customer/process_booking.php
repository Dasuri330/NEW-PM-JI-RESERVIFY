<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: index.php");
    exit();
}

// Database configuration
$host = "localhost";
$user = "root";
$password = "";
$database = "db_pmji";

// Create connection
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the logged-in user's email from session
$user_email = $_SESSION['user_email'];

// Get the corresponding user id from tbl_users
$userIdQuery = "SELECT id FROM tbl_users WHERE email = ?";
$stmt = $conn->prepare($userIdQuery);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows === 0) {
    die("User not found.");
}
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();

// Get booking details from POST request
$event_type = $_POST['event_type'];
$duration = $_POST['duration'];
$reservation_date = $_POST['reservation_date'];
$time_slot = $_POST['time_slot'];
$street_address = $_POST['street_address'];
$barangay = $_POST['barangay'];
$city = $_POST['city'];
$province = $_POST['province'];
$reference_number = $_POST['reference_number'];
$payment_method = $_POST['payment_method'];
$payment_type = $_POST['payment_type'];

// Process file upload for the payment screenshot
$uploadDir = "uploads/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}
if (isset($_FILES['payment_screenshot']) && $_FILES['payment_screenshot']['error'] == 0) {
    // Sanitize file name and generate a unique name to avoid overwrites.
    $fileName = basename($_FILES["payment_screenshot"]["name"]);
    $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
    $newFileName = uniqid("payment_", true) . "." . $fileExt;
    $uploadFilePath = $uploadDir . $newFileName;

    // Check allowed file types (optional)
    $allowed = array("jpg", "jpeg", "png", "gif");
    if (!in_array(strtolower($fileExt), $allowed)) {
        die("Error: Only JPG, JPEG, PNG, & GIF files are allowed.");
    }

    if (!move_uploaded_file($_FILES["payment_screenshot"]["tmp_name"], $uploadFilePath)) {
        die("Error uploading file.");
    }
} else {
    die("Error: Payment screenshot file is required.");
}

// Prepare the insert query for tbl_bookings
$query = "INSERT INTO tbl_bookings 
            (user_id, event_type, duration, reservation_date, time_slot, street_address, barangay, city, province, reference_number, payment_method, payment_type, payment_screenshot)
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($query);

if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param(
    "isissssssssss",
    $user_id,
    $event_type,
    $duration,
    $reservation_date,
    $time_slot,
    $street_address,
    $barangay,
    $city,
    $province,
    $reference_number,
    $payment_method,
    $payment_type,
    $newFileName  // Save file name to be referenced later
);

if ($stmt->execute()) {
    // Successful insertion, redirect to a confirmation or payments page.
    header("Location: booking_success.php");
    exit();
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>