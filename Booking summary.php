<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['id'])) {
    die("You must be logged in to view the booking summary.");
}

// Database connection
require_once "database.php";

// Retrieve the user ID from the session
$user_id = $_SESSION['id'];

// Query to fetch the pinakabagong data mula sa test_registration, reservation, at payment tables
$sql = "
    SELECT 
        tr.First_Name, 
        tr.Middle_Name, 
        tr.Last_Name, 
        tr.Email, 
        r.reservation_id, 
        r.event_type, 
        r.event_place, 
        r.photo_size_layout, 
        r.contact_number, 
        r.start_time,
        r.end_time,
        r.image,
        p.payment_method
    FROM 
        test_registration tr
    LEFT JOIN 
        reservation r ON tr.id = r.user_id
    LEFT JOIN 
        payment p ON r.reservation_id = p.reservation_id
    WHERE 
        tr.id = ?
    ORDER BY 
        r.reservation_id DESC
    LIMIT 1
";

// Prepare and execute the query
$stmt = mysqli_prepare($conn, $sql);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $data = mysqli_fetch_assoc($result);

    if ($data) {
        // Assign fetched data to variables
        $first_name        = $data['First_Name'];
        $middle_name       = $data['Middle_Name'];
        $last_name         = $data['Last_Name'];
        $email             = $data['Email'];
        $reservation_id    = $data['reservation_id'];
        $event_type        = $data['event_type'];
        $event_place       = $data['event_place'];
        $photo_size_layout = $data['photo_size_layout'];
        $contact_number    = $data['contact_number'];
        $start_time        = $data["start_time"]; // raw from DB
        $end_time          = $data["end_time"];   // raw from DB
        $image             = $data['image'];
        $payment_method    = !empty($data['payment_method']) ? $data['payment_method'] : 'Not Specified';
    } else {
        echo "No booking summary available.";
        exit();
    }
} else {
    echo "Database query error: " . mysqli_error($conn);
    exit();
}

// ======== CREATE DISPLAY VERSIONS (12-hour format) ========
$display_start_time = '';
$display_end_time   = '';
if (!empty($start_time)) {
    $display_start_time = date('Y-m-d h:i A', strtotime($start_time));
}
if (!empty($end_time)) {
    $display_end_time = date('Y-m-d h:i A', strtotime($end_time));
}

// Check if Print PDF button (form submission) is triggered
if (isset($_POST['print_pdf'])) {
    // Convert raw start_time and end_time to 24-hour format (for DB)
    $rawStartDB = !empty($start_time) ? date('Y-m-d H:i:s', strtotime($start_time)) : null;
    $rawEndDB   = !empty($end_time)   ? date('Y-m-d H:i:s', strtotime($end_time))   : null;

    // Insert booking summary for the pinakabagong reservation lang
    $insert_sql = "
        INSERT INTO booking_summary
            (user_id, first_name, middle_name, last_name, email, event_type, event_place, 
             photo_size_layout, contact_number, start_time, end_time, image, payment_method, 
             reservation_id, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ";
    
    $stmt_insert = mysqli_prepare($conn, $insert_sql);
    if ($stmt_insert) {
        $status = 'confirmed';
        mysqli_stmt_bind_param(
            $stmt_insert,
            "isssssssssssssi",
            $user_id,
            $first_name,
            $middle_name,
            $last_name,
            $email,
            $event_type,
            $event_place,
            $photo_size_layout,
            $contact_number,
            $rawStartDB,
            $rawEndDB,
            $image,
            $payment_method,
            $reservation_id,
            $status
        );

        if (mysqli_stmt_execute($stmt_insert)) {
            // Get last inserted ID (which is the 'id' field in booking_summary)
            $new_id = mysqli_insert_id($conn);
            // Redirect to PDF generation page in a new tab
            header("Location: generate_bookingsummary_pdf.php?id=$new_id");
            exit();
        } else {
            echo "Error saving reservation: " . mysqli_error($conn);
        }
    } else {
        echo "Error preparing booking summary SQL: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PM&JI Reservify - Booking Summary</title>
    <link rel="stylesheet" href="Booking summary.css?v=1.2">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Inline CSS para madaliang pag-modify -->
    <style>
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: #fff;
            padding: 20px;
            width: 300px;
            text-align: center;
            border-radius: 5px;
        }
        /* Button Styles */
        .buttons {
            margin-top: 20px;
    
        }
        .buttons button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            background: green;
            color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }
        #confirmBtn { /* Palitan ang #confirmBtn ng tamang selector para sa iyong "Confirm" button */
            width: 150px;
        }

        @media (max-width: 768px) {
    .buttons {
        flex-direction: column;
        align-items: center; /* Center buttons horizontally */
    }
    
    .buttons button {
        max-width: 100%;
        margin: 5px 0;
    }
}

    </style>
</head>
<body>
<main>
    <div class="summary-box">
        <h2>Booking Summary</h2>
        <!-- Form para sa Print PDF button lamang -->
        <form method="POST" action="" target="_blank">
            <div class="summary-item">
                <label>Name:</label>
                <input type="text" value="<?php echo htmlspecialchars($first_name . ' ' . $middle_name . ' ' . $last_name); ?>" disabled />
            </div>
            <div class="summary-item">
                <label>Email:</label>
                <input type="text" value="<?php echo htmlspecialchars($email); ?>" disabled />
            </div>
            <div class="summary-item">
                <label>Event Type:</label>
                <input type="text" value="<?php echo htmlspecialchars($event_type); ?>" disabled />
            </div>
            <div class="summary-item">
                <label>Event Place:</label>
                <input type="text" value="<?php echo htmlspecialchars($event_place); ?>" disabled />
            </div>
            <div class="summary-item">
                <label>Photo Size and Layout:</label>
                <input type="text" value="<?php echo htmlspecialchars($photo_size_layout); ?>" disabled />
            </div>
            <div class="summary-item">
                <label>Contact Number:</label>
                <input type="text" value="<?php echo htmlspecialchars($contact_number); ?>" disabled />
            </div>
            <div class="summary-item">
                <label>Start Time:</label>
                <input type="text" value="<?php echo htmlspecialchars($display_start_time); ?>" disabled />
            </div>
            <div class="summary-item">
                <label>End Time:</label>
                <input type="text" value="<?php echo htmlspecialchars($display_end_time); ?>" disabled />
            </div>
            <div class="summary-item">
                <label>Image:</label>
                <?php if (!empty($image)) { ?>
                    <img src="uploads/<?php echo htmlspecialchars($image); ?>" alt="Uploaded Image" style="max-width: 100px; max-height: 100px;">
                <?php } else { ?>
                    <p>No image uploaded</p>
                <?php } ?>
            </div>
            <div class="summary-item">
                <label>Mode of Payment:</label>
                <input type="text" value="<?php echo htmlspecialchars($payment_method); ?>" disabled />
            </div>
            <!-- Parehong buttons sa iisang container, walang space sa gitna ng container -->
            <div class="buttons">
                <button type="button" id="confirmBtn" onclick="openModal()" style="margin-right:10px;">Confirm</button>
            </div>
            <div>
            <button type="submit" id="pdfBtn" name="print_pdf" style=" width: 150px;  background-color: #fac08d;  color: black;  display: block; margin: 20px auto; font-weight: 400; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);"
>
  Print PDF
</button>
            </div>
        </form>
    </div>
</main>

<!-- Modal for Thank You Message -->
<div id="thankYouModal" class="modal">
    <div class="modal-content">
        <p>Thank you for Choosing PM&JI Pictures</p>
        <button onclick="closeModal()">OK</button>
    </div>
</div>

<script>
    // Function to open the modal
    function openModal() {
        document.getElementById("thankYouModal").style.display = "flex";
    }
    // Function to close the modal and redirect to thankyoupage.php
    function closeModal() {
        document.getElementById("thankYouModal").style.display = "none";
        window.location.href = "thankyoupage.php";
    }
</script>
</body>
</html>
