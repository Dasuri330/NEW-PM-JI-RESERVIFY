<?php
session_start();

// PHPMailer use statements (kung kinakailangan, nasa tuktok na)
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Initialize variables
$errors = [];

// Check if the user is logged in
if (!isset($_SESSION['id']) || empty($_SESSION['id'])) {
    die("You must be logged in to make a reservation.");
}

// Get the user ID from the session
$user_id = $_SESSION['id'];

// Process form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["submit"])) {
    // Retrieve and sanitize form inputs
    $event_type        = trim($_POST["event_type"] ?? '');  
    $others            = trim($_POST["other_event"] ?? '');
    $event_place       = trim($_POST["event_place"] ?? '');
    $photo_size_layout = trim($_POST["photo_size_layout"] ?? '');
    $contact_number    = trim($_POST["contact_number"] ?? '');
    // Hidden inputs for validation:
    $start_time        = trim($_POST["start_time"] ?? '');
    $end_time          = trim($_POST["end_time"] ?? '');
    $file_name         = NULL;

    // Validate required fields (excluding image)
    if (empty($event_type) || empty($event_place) || empty($photo_size_layout) || empty($contact_number) || empty($start_time) || empty($end_time)) {
        $errors[] = "All fields are required except the image.";
    }
    if ($event_type === 'others' && empty($others)) {
        $errors[] = "Please specify your event details.";
    }

    // Process image upload only if file is uploaded
    if (!empty($_FILES['image']['name'])) {
        $file_name = $_FILES['image']['name'];
        $file_tmp  = $_FILES['image']['tmp_name'];
        $file_size = $_FILES['image']['size'];
        $upload_dir= "images/";

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (!in_array($file_extension, $allowed_extensions)) {
            $errors[] = "Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.";
        }
        if ($file_size > 5 * 1024 * 1024) {
            $errors[] = "File size exceeds 5MB limit.";
        }
        $target_file = $upload_dir . basename($file_name);
        if (empty($errors)) {
            if (!move_uploaded_file($file_tmp, $target_file)) {
                $errors[] = "File upload failed.";
            }
        }
    } else {
        $file_name = "";
    }

    // Display errors and exit if there are any
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<div class='alert alert-danger'>$error</div>";
        }
        exit();
    }

    // Database connection
    require_once "database.php";

    // Fetch user details
    $user_details_query = "SELECT First_name, Middle_name, Last_Name, Email FROM test_registration WHERE id = ?";
    $user_details_stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($user_details_stmt, $user_details_query)) {
        die("Database error: Unable to prepare user details query.");
    }
    mysqli_stmt_bind_param($user_details_stmt, "i", $user_id);
    mysqli_stmt_execute($user_details_stmt);
    $result = mysqli_stmt_get_result($user_details_stmt);
    if ($row = mysqli_fetch_assoc($result)) {
        $first_name  = $row['First_name'];
        $middle_name = $row['Middle_name'];
        $last_name   = $row['Last_Name'];
        $email       = $row['Email'];
    } else {
        die("User details not found.");
    }
    mysqli_stmt_close($user_details_stmt);

    // Insert reservation with start_time and end_time
    $sql = "INSERT INTO reservation 
        (user_id, event_type, others, event_place, photo_size_layout, contact_number, start_time, end_time, image) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        die("Database error: " . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmt, "issssssss", $user_id, $event_type, $others, $event_place, $photo_size_layout, $contact_number, $start_time, $end_time, $file_name);
    if (mysqli_stmt_execute($stmt)) {
        $reservation_id = mysqli_insert_id($conn);
        $_SESSION['reservation_id'] = $reservation_id;

        // Create notification message
        $notification_title = "New reservation by $first_name $last_name";
        $notification_message = "New reservation by $first_name $last_name ($email).\n"
            . "Event: $event_type @ $event_place\n"
            . "Layout: $photo_size_layout | Time: $start_time - $end_time\n"
            . "Image: " . ($file_name ? $file_name : "No image") . ".\n"
            . "Created At: " . date("Y-m-d H:i:s");
        if ($event_type === 'others' && !empty($others)) {
            $notification_message .= "\nDetails: $others";
        }

        // Insert notification
        $notification_sql = "INSERT INTO admin_notifications 
            (user_id, reservation_id, First_name, Middle_name, Last_Name, Email, event_type, others, event_place, photo_size_layout, contact_number, image, message) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $notification_stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($notification_stmt, $notification_sql)) {
            die("Database error: Unable to prepare notification query.");
        }
        mysqli_stmt_bind_param($notification_stmt, "iisssssssssss", 
            $user_id, $reservation_id, $first_name, $middle_name, $last_name, $email, 
            $event_type, $others, $event_place, $photo_size_layout, $contact_number, $file_name, $notification_message
        );
        if (!mysqli_stmt_execute($notification_stmt)) {
            die("Database error: Unable to execute notification query.");
        }
        mysqli_stmt_close($notification_stmt);

        require 'vendor/autoload.php';
        // PHPMailer code para sabihan ang admin
        $mail = new PHPMailer(true);

        try {
            // SMTP configuration
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'reservifypmji@gmail.com'; // Fixed sender email
            $mail->Password   = 'zxjs nprh gvrn dkny'; // Palitan ng tamang App Password o password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Set sender at recipient
            $mail->setFrom('reservifypmji@gmail.com', 'PM&JI Reservify Notifications');
            $mail->addAddress('reservifypmji@gmail.com'); // Admin email

            // Email Content
            $mail->isHTML(true);
            $mail->Subject = 'New Reservation Received';
            $mail->Body    = "
                <p>Dear Admin,</p>
                <p>A new reservation has been submitted by {$first_name} {$last_name} ({$email}).</p>
                <p>
                    Event: {$event_type} @ {$event_place}<br>
                    Layout: {$photo_size_layout}<br>
                    Time: {$start_time} - {$end_time}
                </p>
                <p>Please log in to PM&JI Reservify to review this reservation and take necessary action.</p>
                <p>Thank you.</p>
            ";

            $mail->send();
        } catch (Exception $e) {
            error_log("Admin email could not be sent. PHPMailer Error: {$mail->ErrorInfo}");
        }

        // Show success modal via JS
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    document.getElementById('customModal').style.display = 'block';
                });
              </script>";
    } else {
        die("Database error: Unable to execute query.");
    }
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PM&JI Reservify - Reservation Form</title>
  <!-- Custom CSS -->
  <link rel="stylesheet" href="reservation.css?v=1.3">
  <!-- Font Awesome for Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"/>
  <!-- FullCalendar -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
  <!-- Ionicons -->
  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

  <style>
    /* Modal CSS */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0; top: 0;
        width: 100%; height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
    }
    .modal-content {
        background-color: white;
        margin: 20% auto;
        padding: 20px;
        width: 300px;
        text-align: center;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0,0,0,0.2);
        position: relative;
    }
    .close {
        position: absolute;
        right: 15px;
        top: 10px;
        font-size: 20px;
        cursor: pointer;
    }
    button {
        padding: 10px 20px;
        background-color: #28a745;
        color: white;
        border: none;
        cursor: pointer;
        border-radius: 5px;
    }
    button:hover {
        background-color: #218838;
    }
    /* Full Image Overlay */
    #fullImageOverlay {
        display: none;
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background-color: rgba(0,0,0,0.8);
        justify-content: center;
        align-items: center;
        z-index: 9999;
        cursor: zoom-out;
    }
    #fullImageOverlay img {
        max-width: 90%;
        max-height: 90%;
        box-shadow: 0 4px 10px rgba(0,0,0,0.5);
        border-radius: 8px;
    }
    .time-slot input[disabled] + label {
        color: gray;
        text-decoration: line-through;
    }
    .fully-booked {
        color: red;
        font-weight: bold;
    }
  </style>
</head>
<body>
     <!-- ========== NAVIGATION BAR START ========== -->
     <nav>
    <!-- LOGO on the left -->
    <div class="logo">
        <a href="About Us.php" onclick="redirectUser()">
        <img src="images/reservify_logo.png" alt="PM&JI logo">
        <span class="logo-text">PM&JI<br>Reservify</span>
        </a>
    </div>
         <!-- MENU that slides out on mobile -->
    <ul class="menu" id="mobile-menu">
        <li><a href="About Us.php">About Us</a></li>
        <li><a href="reservation.php">Reserve Now</a></li>
        <li><a href="customer_mybookings.php">My Bookings</a></li>
        <li><a href="contact_us1.php">Contact Us</a></li>
        <li class="user-logo">
        <a href="profile_user.php">
            <img src="images/user_logo.png" alt="User Logo">
        </a>
        </li>
        <li>
        <div class="notification-container">
            <a href="customer_notification.php">
            <i class="fas fa-envelope" id="notif-icon"></i>
            </a>
        </div>
        </li>
    </ul>
    <!-- HAMBURGER ICON on the right -->
    <div class="toggle" id="hamburger-menu">
        <ion-icon name="menu-outline"></ion-icon>
    </div>
    </nav>
<!-- ========== NAVIGATION BAR END ========== -->
    
<div class="container">
    <div class="header">
        <div class="title">Reservation Form</div>
        <a href="event-rates.php" class="btn-event-rates">Event Rates</a>
    </div>
    <div class="content">
        <form action="reservation.php" method="POST" enctype="multipart/form-data">
            <!-- User Details -->
            <div class="user-details">
                <div class="input-box">
                    <label for="eventType">Event Type:</label>
                    <select id="eventType" name="event_type" required>
                        <option value="" disabled selected>Select Event Type</option>
                        <option value="Wedding Photobooth service for 2 Hours" data-duration="2">Wedding Photobooth service for 2 hours</option>
                        <option value="Wedding Photobooth service for 4 Hours" data-duration="4">Wedding Photobooth service for 4 hours</option>
                        <option value="Reunion Photobooth service for 2 Hours" data-duration="2">Reunion Photobooth service for 2 hours</option>
                        <option value="Reunion Photobooth service for 4 Hours" data-duration="4">Reunion Photobooth service for 4 Hours</option>
                        <option value="Baptism Photobooth service for 2 Hours" data-duration="2">Baptism Photobooth service for 2 Hours</option>
                        <option value="Baptism Photobooth service for 4 Hours" data-duration="4">Baptism Photobooth service for 4 Hours</option>
                        <option value="Birthday Photobooth service for 2 Hours" data-duration="2">Birthday Photobooth service for 2 Hours</option>
                        <option value="Birthday Photobooth service for 4 Hours" data-duration="4">Birthday Photobooth service for 4 Hours</option>
                        <option value="Company event Photobooth service for 2 Hours" data-duration="2">Company Event service for 2 Hours</option>
                        <option value="Company event Photobooth service for 4 Hours" data-duration="4">Company Event service for 4 Hours</option>
                        <option value="others" data-duration="1">Others</option>
                    </select>
                </div>
                <!-- Hidden input for "others" details -->
                <div class="input-box" id="othersInput" style="display: none;">
                    <label for="other_event">Please specify your event details:</label>
                    <input type="text" id="other_event" name="other_event" placeholder="Specify event details">
                </div>
                <div class="input-box">
                    <label for="eventPlace">Event Place:</label>
                    <input id="eventPlace" type="text" name="event_place" placeholder="Enter Event Place" required>
                </div>
                <div class="input-box">
                    <label for="photo-size">Select Photo Size & Layout:</label>
                    <select id="photo-size" name="photo_size_layout" required>
                        <option value="" disabled selected>Select Photo Size & Layout</option>
                        <option value="4x4 4R Size (3 & 4 Grids)">4R Size (3 & 4 Grids)</option>
                        <option value="6x6 Photo Strip Size (2, 3 & 4 Grids)">Photo Strip Size (2, 3 & 4 Grids)</option>
                    </select>
                </div>
                <div class="input-box">
                    <label for="contactNumber">Contact Number:</label>
                    <input 
                        id="contactNumber" 
                        type="tel" 
                        name="contact_number" 
                        placeholder="09123456712" 
                        pattern="0\d{10}" 
                        maxlength="11" 
                        inputmode="numeric" 
                        required 
                        oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                </div>
            </div>

            <!-- Main Container -->
            <div class="main-container">
                <!-- Left Column: Calendar & Time Slots -->
                <div class="left-column">
                    <div id="calendar"></div>
                    <div class="availability">
                        <span class="available">&#9679; Available</span> |
                        <span class="fully-booked">&#9679; Fully Booked</span>
                    </div>
                    <div class="slots-upload-row">
                        <div id="time-slots-container">
                            <div id="time-slots-wrapper">
                                <h3>Time Slots</h3>
                                <div id="label-2">
                                    <h4 id="available-slots-header" style="display: none;">Available Slots For</h4>
                                    <div class="selected-datetime-1"></div>
                                </div>
                                <div id="label-1">
                                    <label for="selected-datetime">Selected Date & Time:</label>
                                    <div class="selected-datetime-1"></div>
                                </div>
                                <!-- Visible (read-only) input for display -->
                                <input type="text" id="selected-datetime" class="selected-datetime-wrapper" name="selected_datetime" readonly required>
                                <!-- Hidden inputs for actual start and end times -->
                                <input type="hidden" name="start_time" id="start_time">
                                <input type="hidden" name="end_time" id="end_time">
                                <div id="time-slots">
                                    <p><em>Select a Date and Event Type to See Available Time Slots.</em></p>
                                </div>
                            </div>
                        </div>
                        <!-- Upload Image Container -->
                        <div class="upload-container">
                            <h2 style="margin-right: 23%;">Upload Image</h2>
                            <p>Assist us in creating a temporary custom background for your selected image.</p>
                            <div class="form-group" style="margin-right: 50px;">
                                <input type="file" name="image" id="imageUpload" accept="image/*">
                            </div>
                            <div class="preview-container">
                                <img id="imagePreview" src="" alt="Image Preview" style="display: none; max-width: 100%; height: auto; margin-top: 10px; cursor: pointer;">
                            </div>
                            <div id="fullImageOverlay">
                                <img id="fullImage" src="" alt="Full Size Image">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Right Column: Additional Images -->
                <div class="images-container">
                    <img src="images/4r.png.png" alt="4R Example">
                    <img src="images/strips.png.png" alt="Strips Example">
                </div>
            </div>

            <!-- Submit -->
            <div class="parent-container">
                <input type="submit" name="submit" class="btn" value="Submit">
            </div>
        </form>

        <!-- Custom Modal for Success -->
        <div id="customModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <h2>Success</h2>
                <p>Reservation submitted successfully! Please wait for 1 hour for your approval.</p>
                <button onclick="redirect()">OK</button>
            </div>
        </div>
    </div>
</div>
        
<!-- Our Works Section & Slideshow -->
<div class="title">
    <h2>Our Works</h2>
</div>
<div class="slideshow-container">
    <!-- Slideshow images -->
    <div class="slide fade"><img src="images/pic1.jpg" alt="pic1" class="normal"></div>
    <div class="slide fade"><img src="images/pic2.jpg" alt="pic2" class="normal"></div>
    <div class="slide fade"><img src="images/pic3.jpg" alt="pic3" class="normal"></div>
    <div class="slide fade"><img src="images/pic4.jpg" alt="pic4" class="normal"></div>
    <div class="slide fade"><img src="images/pic5.jpg" alt="pic5" class="normal"></div>
    <div class="slide fade"><img src="images/pic6.jpg" alt="pic6" class="normal"></div>
    <div class="slide fade"><img src="images/pic7.jpg" alt="pic7" class="normal"></div>
    <div class="slide fade"><img src="images/pic8.jpg" alt="pic8" class="normal"></div>
    <div class="slide fade"><img src="images/pic9.png" alt="pic9" class="normal"></div>
    <div class="slide fade"><img src="images/pic10.jpg" alt="pic10" class="normal"></div>
    <div class="slide fade"><img src="images/pic11.jpg" alt="pic11" class="normal"></div>
    <div class="slide fade"><img src="images/pic12.jpg" alt="pic12" class="normal"></div>
    <div class="slide fade"><img src="images/pic13.jpg" alt="pic13" class="normal"></div>
    <div class="slide fade"><img src="images/pic14.jpg" alt="pic14" class="normal"></div>
    <div class="slide fade"><img src="images/pic15.jpg" alt="pic15" class="normal"></div>
    <div class="slide fade"><img src="images/pic16.jpg" alt="pic16" class="normal"></div>
    <div class="slide fade"><img src="images/pic17.jpg" alt="pic17" class="normal"></div>
    <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
    <a class="next" onclick="plusSlides(1)">&#10095;</a>
</div>
<div class="dots-container">
    <span class="dot" onclick="currentSlide(1)"></span>
    <span class="dot" onclick="currentSlide(2)"></span>
    <span class="dot" onclick="currentSlide(3)"></span>
    <span class="dot" onclick="currentSlide(4)"></span>
    <span class="dot" onclick="currentSlide(5)"></span>
    <span class="dot" onclick="currentSlide(6)"></span>
    <span class="dot" onclick="currentSlide(7)"></span>
    <span class="dot" onclick="currentSlide(8)"></span>
    <span class="dot" onclick="currentSlide(9)"></span>
    <span class="dot" onclick="currentSlide(10)"></span>
    <span class="dot" onclick="currentSlide(11)"></span>
    <span class="dot" onclick="currentSlide(12)"></span>
    <span class="dot" onclick="currentSlide(13)"></span>
    <span class="dot" onclick="currentSlide(14)"></span>
    <span class="dot" onclick="currentSlide(15)"></span>
    <span class="dot" onclick="currentSlide(16)"></span>
    <span class="dot" onclick="currentSlide(17)"></span>
</div>

<footer>
    <p>&copy; 2025 PM&JI Reservify. All Rights Reserved.</p>
</footer>

<a href="connect_with_us_1.php" class="message-link">
    <div class="message-icon">
        <i class="fa fa-message"></i>
        <span>Connect with Us</span>
    </div>
</a>

<!-- JavaScript Section -->
<script>
    // Toggle display of 'others' input box
    document.getElementById('eventType').addEventListener('change', function() {
        var othersInput = document.getElementById('othersInput');
        if (this.value === 'others') {
            othersInput.style.display = 'block';
        } else {
            othersInput.style.display = 'none';
        }
    });

    // Hamburger Menu Toggle
    const hamburgerMenu = document.getElementById('hamburger-menu');
    const mobileMenu = document.getElementById('mobile-menu');
    const hamburgerIcon = hamburgerMenu.querySelector('ion-icon');

    hamburgerMenu.addEventListener('click', () => {
        mobileMenu.classList.toggle('active');
        if (mobileMenu.classList.contains('active')) {
            hamburgerIcon.setAttribute('name', 'close-outline');
        } else {
            hamburgerIcon.setAttribute('name', 'menu-outline');
        }
    });

    // Combined DOMContentLoaded: initialize calendar, event listeners, slides, notifications.
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        const today = new Date().toISOString().split('T')[0];
        let defaultDuration = 3;
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            selectable: true,
            dateClick: function(info) {
                if (info.dateStr >= today) {
                    loadTimeSlots(info.dateStr, defaultDuration);
                }
            },
            events: [
                { title: 'Available', start: '2025-02-15', color: 'green' },
                { title: 'Fully Booked', start: '2025-02-20', color: 'red' }
            ],
            dayCellDidMount: function(info) {
                const cellDate = info.date.toISOString().split('T')[0];
                if (cellDate < today) {
                    info.el.classList.add('past');
                }
            }
        });
        calendar.render();

        const eventTypeDropdown = document.getElementById("eventType");
        eventTypeDropdown.addEventListener("change", function() {
            defaultDuration = parseInt(eventTypeDropdown.options[eventTypeDropdown.selectedIndex].dataset.duration) || 3;
            const selectedDate = document.getElementById("available-slots-header").dataset.selectedDate;
            if (selectedDate) {
                loadTimeSlots(selectedDate, defaultDuration);
            }
        });

        document.querySelector("form").addEventListener("submit", function(e) {
            const selectedDateTimeInput = document.getElementById("selected-datetime");
            if (!selectedDateTimeInput.value) {
                alert("Please select a date and time slot before submitting.");
                e.preventDefault();
            }
        });

        showSlides();
        fetchNotifications();
    });

    function loadTimeSlots(date, duration) {
        const timeSlotContainer = document.getElementById('time-slots');
        const startTimeInput = document.getElementById('start_time');
        const endTimeInput = document.getElementById('end_time');
        const selectedDateTimeInput = document.getElementById("selected-datetime");
        const slotsHeader = document.getElementById("available-slots-header");

        slotsHeader.style.display = "block";
        slotsHeader.innerHTML = `Available slots for ${date}`;
        slotsHeader.dataset.selectedDate = date;
        timeSlotContainer.innerHTML = "";

        $.ajax({
            url: 'fetch_booked_slots.php',
            method: 'GET',
            data: { date: date },
            dataType: 'json',
            success: function(adminEvents) {
                let startTime = 9;
                const endTime = 17.5;
                let slots = [];
                while (startTime + duration <= endTime) {
                    const startDisplay = convertTo12HourFormat(startTime);
                    const endDisplay = convertTo12HourFormat(startTime + duration);
                    let slot = {
                        display: `${startDisplay} - ${endDisplay}`,
                        start: convertToISO(date, startTime),
                        end: convertToISO(date, startTime + duration)
                    };
                    slots.push(slot);
                    startTime += duration;
                }
                slots.forEach(slot => {
                    let slotStart = new Date(slot.start);
                    let slotEnd = new Date(slot.end);
                    let isUnavailable = adminEvents.some(event => {
                        let eventStart = new Date(event.start_time);
                        let eventEnd = new Date(event.end_time);
                        return (slotStart < eventEnd && slotEnd > eventStart);
                    });

                    const slotElement = document.createElement("div");
                    slotElement.classList.add("time-slot");

                    slotElement.innerHTML = `
                        <input type="radio" name="time_slot" value="${slot.display}" ${isUnavailable ? 'disabled' : ''}>
                        <label ${isUnavailable ? 'style="color: red; text-decoration: line-through;"' : ''}>
                            ${slot.display}
                        </label>
                        <span style="${isUnavailable 
                            ? 'color: red; text-decoration: none; font-weight: bold;' 
                            : 'color: green; font-weight: bold;'}">
                            ${isUnavailable ? 'Already Booked' : 'Available'}
                        </span>
                    `;

                    if (!isUnavailable) {
                        slotElement.querySelector("input").addEventListener("change", function() {
                            startTimeInput.value = slot.start;
                            endTimeInput.value = slot.end;
                            selectedDateTimeInput.value = date + " " + slot.display;
                        });
                    }

                    timeSlotContainer.appendChild(slotElement);
                });
            },
            error: function(xhr, status, error) {
                console.error("Error fetching admin event times: ", error);
            }
        });
    }

    function convertTo12HourFormat(hourDecimal) {
        let hrs = Math.floor(hourDecimal);
        let mins = Math.round((hourDecimal - hrs) * 60);
        const period = hrs >= 12 ? "PM" : "AM";
        let displayHour = hrs % 12;
        if (displayHour === 0) displayHour = 12;
        let displayMinutes = mins < 10 ? "0" + mins : mins;
        return `${displayHour}:${displayMinutes} ${period}`;
    }

    function convertToISO(date, hourDecimal) {
        let hrs = Math.floor(hourDecimal);
        let mins = Math.round((hourDecimal - hrs) * 60);
        let hh = hrs < 10 ? "0" + hrs : hrs;
        let mm = mins < 10 ? "0" + mins : mins;
        return `${date}T${hh}:${mm}:00`;
    }

    let currentIndex = 1;
    function showSlides() {
        const slides = document.querySelectorAll(".slide");
        const dots = document.querySelectorAll(".dot");
        slides.forEach(slide => slide.style.display = "none");
        dots.forEach(dot => dot.classList.remove("active"));
        if (currentIndex > slides.length) currentIndex = 1;
        if (currentIndex < 1) currentIndex = slides.length;
        slides[currentIndex - 1].style.display = "block";
        dots[currentIndex - 1].classList.add("active");
        currentIndex++;
        setTimeout(showSlides, 3000);
    }
    function currentSlide(index) {
        currentIndex = index;
        showSlides();
    }
    function plusSlides(n) {
        const slides = document.querySelectorAll(".slide");
        currentIndex += n;
        if (currentIndex < 1) currentIndex = slides.length;
        if (currentIndex > slides.length) currentIndex = 1;
        showSlides();
    }

    async function fetchNotifications() {
        try {
            const response = await fetch('fetch_notification.php');
            const notifications = await response.json();
            const dropdown = document.querySelector(".notification-dropdown");
            if (notifications.length > 0) {
                document.querySelector('.notification-count').textContent = notifications.length;
                dropdown.innerHTML = notifications.map(notification => {
                    let notificationTime = new Date(notification.time);
                    if (isNaN(notificationTime)) {
                        console.error("Invalid date:", notification.time);
                        notificationTime = new Date();
                    }
                    const formattedTime = notificationTime.toLocaleString('en-US', {
                        weekday: 'short', year: 'numeric', month: 'short', day: 'numeric',
                        hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true
                    });
                    return `<div class="notification-item">${notification.message} <span class="time">${formattedTime}</span></div>`;
                }).join("");
            } else {
                dropdown.innerHTML = "<p>No new notifications</p>";
            }
        } catch (error) {
            console.error('Error fetching notifications:', error);
            document.querySelector(".notification-dropdown").innerHTML = "<p>Failed to load notifications</p>";
        }
    }

    document.getElementById('imageUpload').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const imagePreview = document.getElementById('imagePreview');
        const previewContainer = document.querySelector('.preview-container');
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                imagePreview.style.display = 'block';
                previewContainer.style.minHeight = "220px";
            }
            reader.readAsDataURL(file);
        } else {
            imagePreview.src = '';
            imagePreview.style.display = 'none';
        }
    });
    document.getElementById('imagePreview').addEventListener('click', function() {
        const fullImage = document.getElementById('fullImage');
        const fullImageOverlay = document.getElementById('fullImageOverlay');
        fullImage.src = this.src;
        fullImageOverlay.style.display = 'flex';
    });
    document.getElementById('fullImageOverlay').addEventListener('click', function(event) {
        if (event.target === this) {
            this.style.display = 'none';
        }
    });

    function closeModal() {
        document.getElementById('customModal').style.display = 'none';
    }
    function redirect() {
        window.location.href = 'reservation.php';
    }
    function redirectUser() {
        window.location.href = "<?php echo isset($isLoggedIn) && $isLoggedIn ? 'AboutUs.php' : 'Home.php'; ?>";
    }

    const notifBell = document.getElementById("notif-icon");
    if (notifBell) {
        notifBell.addEventListener("click", function(event) {
            event.stopPropagation();
            const dropdown = document.querySelector(".notification-dropdown");
            if (dropdown) dropdown.classList.toggle("show");
        });
    }
    document.addEventListener("click", function(event) {
        const dropdown = document.querySelector(".notification-dropdown");
        if (dropdown && !event.target.closest("#notif-icon")) {
            dropdown.classList.remove("show");
        }
    });
  </script>
</body>
</html>
