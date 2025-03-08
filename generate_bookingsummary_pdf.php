<?php
session_start();

// Kung gusto mo i-check kung naka-login:
if (!isset($_SESSION['id'])) {
    die("Not logged in.");
}

// Kailangan mo i-include ang TCPDF library
require_once(__DIR__ . '/TCPDF-main/tcpdf.php');

// Konek ulit sa database
require_once('database.php');

// Kunin ang id mula sa URL
if (!isset($_GET['id'])) {
    die("No summary ID provided.");
}
$id = intval($_GET['id']);

// Kunin mula sa booking_summary table
$sql = "SELECT * FROM booking_summary WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$summaryData = $result->fetch_assoc();
$stmt->close();
$conn->close();

// Kung wala
if (!$summaryData) {
    die("Booking summary record not found.");
}

// I-convert ang start_time at end_time sa 12-hour format
$convertedStart = '';
$convertedEnd   = '';
if (!empty($summaryData['start_time'])) {
    $convertedStart = date("Y-m-d h:i A", strtotime($summaryData['start_time']));
}
if (!empty($summaryData['end_time'])) {
    $convertedEnd = date("Y-m-d h:i A", strtotime($summaryData['end_time']));
}

// Gumawa ng TCPDF object
$pdf = new TCPDF();

// Set document info (opsyonal)
$pdf->SetCreator('PM&JI Reservify');
$pdf->SetAuthor('PM&JI');
$pdf->SetTitle('Booking Summary');
$pdf->SetSubject('Reservation Details');

// Add a page
$pdf->AddPage();

// Gumawa ng HTML content
$html = '
<h1 style="text-align:center;">Booking Summary</h1>
<p><strong>Name:</strong> ' . htmlspecialchars($summaryData['first_name'] . ' ' . $summaryData['middle_name'] . ' ' . $summaryData['last_name']) . '</p>
<p><strong>Email:</strong> ' . htmlspecialchars($summaryData['email']) . '</p>
<p><strong>Event Type:</strong> ' . htmlspecialchars($summaryData['event_type']) . '</p>
<p><strong>Event Place:</strong> ' . htmlspecialchars($summaryData['event_place']) . '</p>
<p><strong>Contact:</strong> ' . htmlspecialchars($summaryData['contact_number']) . '</p>
<p><strong>Payment Method:</strong> ' . htmlspecialchars($summaryData['payment_method']) . '</p>
';

// I-append ang start at end time sa 12-hour format
$html .= '<p><strong>Start Time:</strong> ' . htmlspecialchars($convertedStart) . '</p>
<p><strong>End Time:</strong> ' . htmlspecialchars($convertedEnd) . '</p>
';

// Kung gusto mong ilagay ang image (na-upload)
if (!empty($summaryData['image'])) {
    $imgPath = 'uploads/' . htmlspecialchars($summaryData['image']);
    $html .= '
    <p><strong>Uploaded Image:</strong></p>
    <p><img src="' . $imgPath . '" width="150" height="150"></p>
    ';
}

// I-render ang HTML gamit ang writeHTML()
$pdf->writeHTML($html, true, false, true, false, '');

// I-output ang PDF sa browser
$pdf->Output('booking_summary.pdf', 'I');
