<?php
session_start();
// Check if admin is logged in; if not, redirect to the login page.
if (!isset($_SESSION['admin_id'])) {
    header("Location: /NEW-PM-JI-RESERVIFY/pages/admin/admin_login.php");
    exit;
}
$admin_username = $_SESSION['admin_username'];

// Database connection configuration.
$host = 'localhost';
$db = 'db_pmji';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// Fetch approved bookings for the calendar. (Assumes booking status 'approved'.)
$stmt = $pdo->prepare("SELECT * FROM tbl_bookings WHERE status = 'approved'");
$stmt->execute();
$approvedBookings = $stmt->fetchAll();

// Build events array for FullCalendar.
$events = [];
foreach ($approvedBookings as $booking) {
    $date = $booking['reservation_date'];
    $timeSlot = trim($booking['time_slot']);

    // Default start and end times if no recognizable slot.
    $startTime = "00:00:00";
    $endTime = "23:59:59";

    if (stripos($timeSlot, "morning") !== false) {
        $startTime = "08:00:00";
        $endTime = "12:00:00";
    } elseif (stripos($timeSlot, "afternoon") !== false) {
        $startTime = "13:00:00";
        $endTime = "17:00:00";
    } elseif (stripos($timeSlot, "evening") !== false) {
        $startTime = "18:00:00";
        $endTime = "22:00:00";
    }

    // Build the event title as "Time Slot - Event Type"
    $events[] = [
        "id" => $booking['id'],
        "title" => $timeSlot . " - " . trim($booking['event_type']),
        "start" => $date . "T" . $startTime,
        "end" => $date . "T" . $endTime,
    ];
}
$eventsJson = json_encode($events);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin - Calendar</title>
    <!-- FullCalendar Global Bundle -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet" />
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom Admin Dashboard CSS -->
    <link rel="stylesheet" href="../index.css">
    <!-- Custom Calendar CSS -->
    <link rel="stylesheet" href="calendar.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>

<body>
    <!-- Include common header -->
    <?php include '../components/admin_header.php'; ?>

    <!-- Dashboard Container: Sidebar + Main Content -->
    <div class="dashboard-container">
        <!-- Include sidebar -->
        <?php include '../components/admin_sidebar.php'; ?>

        <!-- Main Content Section -->
        <main class="main-content">
            <div class="container">
                    <div id="calendar"></div>
            </div>
        </main>
    </div>

    <!-- FullCalendar Global Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                height: 600, // Control calendar height via options
                events: <?php echo $eventsJson; ?>
            });
            calendar.render();
        });
    </script>
</body>

</html>