<?php
session_start();
require_once "database.php";

// Ipinapalagay na ang admin's ID ay naka-store sa session pagkatapos mag-login
$admin_ID = isset($_SESSION['admin_ID']) ? $_SESSION['admin_ID'] : 'AD-0001';

// Pag-handle ng logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin_login.php');
    exit();
}

// Pagkuha ng events mula sa reservation at test_registration tables
$events = [];

// Helper function para ma-parse kung ilang oras ang nakalagay sa event_type
function getHoursFromEventType($eventType) {
    $pattern = '/(\d+)\s*hours/i'; 
    if (preg_match($pattern, $eventType, $matches)) {
        return (int) $matches[1];
    }
    return null;
}

$sql = "SELECT 
            tr.first_name, tr.middle_name, tr.last_name, tr.email, 
            r.event_type, r.event_place, r.photo_size_layout, 
            r.contact_number, r.start_time AS event_start, r.end_time,
            r.image, r.message, r.status
        FROM test_registration tr
        JOIN reservation r ON tr.id = r.user_id
        JOIN payment p ON p.reservation_id = r.reservation_id
        WHERE r.status = 'approved' AND p.status = 'approved'";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // 1) Kunin ang start_time bilang DateTime object
        $startDate = new DateTime($row['event_start']);

        // 2) Tingnan kung may hours sa event_type
        $hours = getHoursFromEventType($row['event_type']);
        $computedEnd = null;

        if ($hours) {
            $endDate = clone $startDate;
            $endDate->modify("+{$hours} hours");
            $computedEnd = $endDate->format('Y-m-d H:i:s');
        } else {
            // Fallback: gamitin ang end_time mula sa database o gawing kapareho ng start_time kung wala
            $computedEnd = !empty($row['end_time']) ? $row['end_time'] : $row['event_start'];
        }

        // Pagsamahin ang first_name at last_name para sa customer_name
        $customer_name = $row['first_name'] . " " . $row['last_name'];
        $event_type = $row['event_type'];
        $event_place = $row['event_place'];
        $photo_size_layout = $row['photo_size_layout'];
        $contact_number = $row['contact_number'];
        $event_start = $row['event_start'];
        $event_end = $computedEnd;

        // Mag-check muna kung ang record ay hindi pa naiinsert
        $sql_check = "SELECT id FROM admin_eventcalendar WHERE event_start = ? AND contact_number = ? LIMIT 1";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("ss", $event_start, $contact_number);
        $stmt_check->execute();
        $stmt_check->store_result();
        
        if ($stmt_check->num_rows === 0) {
            // Walang duplicate, isagawa ang INSERT
            $sql_insert = "INSERT INTO admin_eventcalendar 
                (customer_name, event_type, event_place, photo_size_layout, contact_number, event_start, event_end) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("sssssss", 
                $customer_name, 
                $event_type, 
                $event_place, 
                $photo_size_layout, 
                $contact_number, 
                $event_start, 
                $event_end
            );
            if (!$stmt_insert->execute()) {
                error_log("Insert error: " . $stmt_insert->error);
            }
            $stmt_insert->close();
        }
        $stmt_check->close();

        // Ihanda ang data para sa FullCalendar events array
        $events[] = [
            'title' => 'Event for ' . $row['first_name'] . ' ' . $row['last_name'],
            'start' => $row['event_start'],
            'end'   => $computedEnd,
            'extendedProps' => [
                'first_name' => $row['first_name'],
                'middle_name' => $row['middle_name'],
                'last_name' => $row['last_name'],
                'email' => $row['email'],
                'event_type' => $row['event_type'],
                'event_place' => $row['event_place'],
                'photo_size_layout' => $row['photo_size_layout'],
                'contact_number' => $row['contact_number'],
                'image' => $row['image'],
                'message' => $row['message'],
                'end_time' => $row['end_time']
            ]
        ];
    }
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Calendar</title>
    <!-- Include FontAwesome CDN (kung wala pa) -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="admin_calendar.css?v=1.2">
    <link rel="stylesheet" href="admin_dashboard.css?v=1.2">
    <link rel="stylesheet" href="admin_bookinghistory.css">
    <link rel="stylesheet" href="admin_profile.css?v=1.1">
    <link rel="stylesheet" href="admin_bookings.css?v=1.1">
    <link rel="stylesheet" href="admin_payments.css?v=1.1">
    <link rel="stylesheet" href="admin_managefeedback.css">
</head>
<body>
<div class="admin-dashboard">
    <aside class="sidebar">
        <div class="logo">
            <img src="images/reservify_logo.png" alt="Reservify Logo">
            <p>Hello, Admin!</p>
        </div>
        <nav>
            <ul>
                <li class="dashboard-item">
                    <a href="admin_dashboard.php" style="display: flex; align-items: center; gap: 7px;">
                        <img src="images/home.png" alt="Home Icon">
                        <span style="margin-left: 1px; margin-top: 4px; color: black;">Dashboard</span>
                    </a>
                </li>
            </ul>
            <hr class="divider">
            <ul>
                <li>
                    <a href="admin_bookings.php" style="text-decoration: none; color: black; display: flex; justify-content: space-between; align-items: center;">
                        <span>Bookings</span>
                        <img class="click-here" src="images/click_here.png.png" alt="Click Here">
                    </a>
                </li>
                <li>
                    <a href="admin_payments1.php" style="text-decoration: none; color: black; display: flex; justify-content: space-between; align-items: center;">
                        <span>Payments</span>
                        <img class="click-here" src="images/click_here.png.png" alt="Click Here">
                    </a>
                </li>
                <li>
                    <a href="admin_payments.php" style="text-decoration: none; color: black; display: flex; justify-content: space-between; align-items: center;">
                        <span>Payment Records</span>
                        <img class="click-here" src="images/click_here.png.png" alt="Click Here">
                    </a>
                </li>
                <li>
                    <a href="admin_bookinghistory.php" style="text-decoration: none; color: black; display: flex; justify-content: space-between; align-items: center;">
                        <span>Booking History</span>
                        <img class="click-here" src="images/click_here.png.png" alt="Click Here">
                    </a>
                </li>
                <li>
                    <a href="admin_managefeedback.php" style="text-decoration: none; color: black; display: flex; justify-content: space-between; align-items: center;">
                        <span>Manage Feedback</span>
                        <img class="click-here" src="images/click_here.png.png" alt="Click Here">
                    </a>
                </li>
                <li>
                    <a href="admin_calendar.php" style="text-decoration: none; color: black; display: flex; justify-content: space-between; align-items: center;">
                        <span>Calendar</span>
                        <img class="click-here" src="images/click_here.png.png" alt="Click Here">
                    </a>
                </li>
            </ul>
            <hr class="divider">
            <ul>
                <li>
                    <a href="admin_manageinq.php" style="text-decoration: none; color: black; display: flex; justify-content: space-between; align-items: center;">
                        <span>Manage Inquiries</span>
                        <img class="click-here" src="images/click_here.png.png" alt="Click Here">
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <main class="content">
        <header class="header">
            <h1 style="color: black;">Calendar</h1>
            <div class="header-right">
                <div class="notification-container">
                    <a href="admin_view_notification.php">
                        <i class="fas fa-envelope" id="notif-icon"></i>
                    </a>
                </div>
                <div class="profile-container">
                    <img class="profile-icon" src="images/user_logo.png" alt="Profile Icon" onclick="toggleDropdown()">
                    <div id="profile-dropdown" class="dropdown">
                        <p class="dropdown-header"><?php echo htmlspecialchars($admin_name); ?></p>
                        <hr>
                        <ul>
                            <li><a href="admin_profile.php">Profile</a></li>
                            <li><a href="admin_activitylog.php">Activity Log</a></li>
                        </ul>
                        <hr>
                        <a class="logout" href="?logout">Logout</a>
                    </div>
                </div>
            </div>
        </header>
        
        <style>
            .popup-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.7);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 1000;
                font-family: "Poppins", sans-serif;
            }
            .popup-content {
                background-color: white;
                padding: 20px;
                border-radius: 8px;
                width: 400px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
                text-align: left;
                position: relative;
                font-family: "Poppins", sans-serif;
            }
            .modal-title {
                text-align: center;
                font-size: 20px;
                font-weight: bold;
                margin-bottom: 15px;
                font-family: "Poppins", sans-serif;
            }
            .modal-close-btn {
                display: block;
                width: 100%;
                margin-top: 15px;
                padding: 10px;
                background-color: #fac08d;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                text-align: center;
                color: black;
                font-family: "Poppins", sans-serif;
            }
            .modal-close-btn:hover {
                background-color: #fac08d;
            }
            #notif-icon {
              color: black;
              font-size: 24px;
            }
            
        </style>

        <div id="calendar"></div>
    </main>
</div>

<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        // Kuhanin ang events array mula sa PHP
        var events = <?php echo json_encode($events); ?>;
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'timeGridWeek', 
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'timeGridDay,timeGridWeek,dayGridMonth'
            },
            events: events,
            editable: true,
            selectable: true,
            selectHelper: true,
            eventClick: function(info) {
                openEventDetailsModal(info.event);
            }
        });
        calendar.render();

        function openEventDetailsModal(event) {
            console.log("Event Clicked:", event);
            if (!event.extendedProps) {
                console.error("Walang extendedProps sa event:", event);
                return;
            }
            const startTime = event.start 
                ? event.start.toLocaleString('en-US', { hour12: true }) 
                : 'N/A';
            const endTime = event.end 
                ? event.end.toLocaleString('en-US', { hour12: true }) 
                : 'N/A';
            const eventDetails = `
                <h2 class="modal-title">Event Details</h2>
                <strong>Customer Name:</strong> ${event.extendedProps.first_name} ${event.extendedProps.last_name}<br>
                <strong>Email:</strong> ${event.extendedProps.email}<br>
                <strong>Event Type:</strong> ${event.extendedProps.event_type}<br>
                <strong>Event Place:</strong> ${event.extendedProps.event_place}<br>
                <strong>Photo Size/Layout:</strong> ${event.extendedProps.photo_size_layout}<br>
                <strong>Contact Number:</strong> ${event.extendedProps.contact_number}<br>
                <strong>Start Time:</strong> ${startTime}<br>
                <strong>End Time:</strong> ${endTime}<br>
            `;
            var existingPopup = document.querySelector('.popup-overlay');
            if (existingPopup) {
                existingPopup.remove();
            }
            var popupOverlay = document.createElement('div');
            popupOverlay.classList.add('popup-overlay');
            var popupContent = document.createElement('div');
            popupContent.classList.add('popup-content');
            popupContent.innerHTML = eventDetails;
            var closeButton = document.createElement('button');
            closeButton.innerText = 'Close';
            closeButton.classList.add('modal-close-btn');
            closeButton.onclick = function() {
                document.body.removeChild(popupOverlay);
            };
            popupContent.appendChild(closeButton);
            popupOverlay.appendChild(popupContent);
            document.body.appendChild(popupOverlay);
            popupOverlay.style.display = 'flex';
        }
    });
</script>
</body>
</html>
