<?php
session_start();
require_once "database.php";

// Kunin ang admin ID mula sa session
$admin_ID = isset($_SESSION['admin_ID']) ? $_SESSION['admin_ID'] : 'AD-0001';

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin_login.php');
    exit();
}

// 1) Kunin ang admin notifications
$notifications = [];
$sql = "SELECT * FROM admin_notifications WHERE admin_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("s", $admin_ID);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
    $stmt->close();
} else {
    echo "Error: " . $conn->error;
}

// 2) Kunin ang reservations
$sql = "
SELECT
  r.reservation_id,
  r.user_id,
  r.event_type,
  r.others,
  r.event_place,
  r.photo_size_layout,
  r.contact_number,
  r.start_time,
  r.end_time,
  r.image,
  r.message,
  r.status,
  u.First_Name,
  u.Middle_Name,
  u.Last_Name,
  u.Email,

  /* KUNIN LANG ‘YUNG PINAKAHULING (o MAX) reject_reason */
  cn.reject_reason

FROM reservation r
JOIN test_registration u 
    ON r.user_id = u.id

LEFT JOIN (
    SELECT 
       reservation_id, 
       MAX(reject_reason) AS reject_reason
    FROM customer_notifications
    GROUP BY reservation_id
) cn
    ON r.reservation_id = cn.reservation_id

ORDER BY r.reservation_id ASC
";

$result = $conn->query($sql);
if (!$result) {
    die("Error: " . $conn->error);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin PM&JI Reservify</title>
    <link rel="stylesheet" href="admin_dashboard.css?v=1.1">
    <link rel="stylesheet" href="admin_profile.css?v=1.1">
    <link rel="stylesheet" href="admin_activitylog.css?v=1.1">
    <link rel="stylesheet" href="admin_bookings.css?v=1.1">
    <!-- Include FontAwesome CDN (if not already included) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
                    <a href="admin_payments.php"style="text-decoration: none; color: black; display: flex; justify-content: space-between; align-items: center;">
                        <span>Payment Records</span>
                        <img class="click-here" src="images/click_here.png.png" alt="Click Here">
                    </a>
                </li>
                <li>
                    <a href="admin_bookinghistory.php"style="text-decoration: none; color: black; display: flex; justify-content: space-between; align-items: center;">
                        <span>Booking History</span>
                        <img class="click-here" src="images/click_here.png.png" alt="Click Here">
                    </a>
                </li>
                <li>
                    <a href="admin_managefeedback.php"style="text-decoration: none; color: black; display: flex; justify-content: space-between; align-items: center;">
                        <span>Manage Feedback</span>
                        <img class="click-here" src="images/click_here.png.png" alt="Click Here">
                    </a>
                </li>
                <li>
                    <a href="admin_calendar.php"style="text-decoration: none; color: black; display: flex; justify-content: space-between; align-items: center;">
                        <span>Calendar</span>
                        <img class="click-here" src="images/click_here.png.png" alt="Click Here">
                    </a>
                </li>
            </ul>
            <hr class="divider">
            <ul>
                <li>
                    <a href="admin_manageinq.php"style="text-decoration: none; color: black; display: flex; justify-content: space-between; align-items: center;">
                        <span>Manage Inquiries</span>
                        <img class="click-here" src="images/click_here.png.png" alt="Click Here">
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <div class="content">
        <div class="header-right">
            <main class="booking-status">
                <header>
                    <h1>Booking Status</h1>
                    <div style="display: flex; align-items: center; gap: 20px;">
                        <!-- Search bar -->
                        <input
                            type="text"
                            id="searchBar"
                            placeholder="Search reservation number.."
                            onkeyup="searchTable()"
                        >
                        <!--Notification part-->
                        <div class="notification-container">
                            <a href="admin_view_notification.php">
                                <i class="fas fa-envelope" id="notif-icon"></i>
                            </a>
                        </div>
                        <!-- Profile Icon -->
                        <div class="profile-container">
                            <img
                                class="profile-icon"
                                src="images/user_logo.png"
                                alt="Profile Icon"
                            >
                            <div id="profile-dropdown" class="dropdown">
                                <p class="dropdown-header">Jiar Cabubas (Admin)</p>
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

                <!-- Table of reservations -->
                <table>
                    <thead>
                        <tr>
                            <th>Reservation Number</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                // Isama na ang petsa at oras (Month Day, Year, Hour:Minute AM/PM)
                                $start_12 = date("F j, Y, g:i A", strtotime($row['start_time']));
                                $end_12   = date("F j, Y, g:i A", strtotime($row['end_time']));

                                $reservation_id = htmlspecialchars($row['reservation_id']);
                                $status = htmlspecialchars($row['status']);
                                $reject_reason = isset($row['reject_reason'])
                                    ? htmlspecialchars($row['reject_reason'])
                                    : '';

                                // Kulay ng status
                                $status_color = ($status == 'approved')
                                    ? 'green'
                                    : (($status == 'rejected')
                                        ? 'red'
                                        : 'gray'
                                    );

                                echo "<tr class='reservation-row'
                                    data-reservation-id='{$reservation_id}'
                                    data-name='" . htmlspecialchars($row['First_Name'] . ' ' . $row['Middle_Name'] . ' ' . $row['Last_Name']) . "'
                                    data-email='" . htmlspecialchars($row['Email']) . "'
                                    data-event-type='" . htmlspecialchars($row['event_type']) . "'
                                    data-others='" . htmlspecialchars($row['others']) . "'
                                    data-event-place='" . htmlspecialchars($row['event_place']) . "'
                                    data-photo-size-layout='" . htmlspecialchars($row['photo_size_layout']) . "'
                                    data-contact-number='" . htmlspecialchars($row['contact_number']) . "'
                                    /* Ito na ang converted times */
                                    data-start-time='" . htmlspecialchars($start_12) . "'
                                    data-end-time='"   . htmlspecialchars($end_12)   . "'
                                    data-image='" . htmlspecialchars($row['image']) . "'
                                    data-reason='" . ($status == 'rejected' ? $reject_reason : '') . "'
                                    data-status='{$status}'
                                    style='cursor: pointer;'
                                >
                                    <td>{$reservation_id}</td>
                                    <td style='color: {$status_color};'>{$status}</td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='2'>No reservations found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>

                <!-- Main Modal -->
                <div id="reservation-modal" class="modal">
                    <div class="modal-content">
                        <span class="close-btn">&times;</span>
                        <h2 style="text-align: center;">Reservation Details</h2>
                        <p id="modal-name"></p>
                        <p id="modal-email"></p>
                        <p id="modal-event-type"></p>
                        <p id="modal-others"></p>
                        <p id="modal-event-place"></p>
                        <p id="modal-photo-size-layout"></p>
                        <p id="modal-contact-number"></p>
                        <p id="modal-start-time"></p>
                        <p id="modal-end-time"></p>
                        <img id="modal-image" src="" alt="Event Image" style="max-width: 50%;">
                        <p id="modal-status"></p>
                        <p id="modal-reason" style="color: red; font-weight: bold;"></p>

                        <!-- Approve and Reject Buttons -->
                        <form action="handle_notification1.php" method="POST">
                            <input type="hidden" name="reservation_id" id="modal-reservation-id">
                            <div class="button-container">
                                <button type="submit" name="action" value="approve" class="approve-button">
                                    Approve
                                </button>
                                <button type="button" id="reject-button" class="reject-button">
                                    Reject
                                </button>
                                <a href="admin_bookings.php" class="back-button">Back to Bookings</a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Reject Modal -->
                <div id="reject-modal" class="modal">
                    <div class="modal-content">
                        <span class="close-button" id="close-modal">&times;</span>
                        <h2>Reason for Rejection</h2>
                        <form id="reject-form" action="handle_notification1.php" method="post">
                            <textarea
                                id="reject-reason"
                                name="reject_reason"
                                rows="4"
                                placeholder="Enter reason for rejection"
                            ></textarea>
                            <div class="modal-actions">
                                <button
                                    type="submit"
                                    name="action"
                                    value="reject"
                                    class="submit-reject-button"
                                >
                                    Submit
                                </button>
                            </div>
                            <input type="hidden" name="reservation_id" id="reject-reservation-id">
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>

<!-- ================== STYLES ================== -->
<style>
    .reservation-row:hover {
        background-color: #f5f5f5;
    }
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0; top: 0;
        width: 100%; height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        align-items: center; justify-content: center;
    }
    .modal-content {
        background: white;
        padding: 20px;
        border-radius: 8px;
        width: 60%;
        max-width: 600px;
        box-shadow: 0 0 10px rgba(0,0,0,0.3);
        position: relative;
        margin: auto;
    }
    .close-btn, .close-button {
        position: absolute;
        top: 10px; right: 15px;
        font-size: 24px; cursor: pointer;
    }
    #modal-image {
        max-width: 400px;
        max-height: 250px;
        margin-top: 10px;
        border-radius: 6px;
    }
    .button-container {
        display: flex; gap: 10px;
        margin-top: 20px;
    }
    .approve-button {
        background-color: #28a745;
        color: white; border: none;
        padding: 10px 20px; font-size: 16px;
        cursor: pointer; border-radius: 5px;
    }
    .approve-button:hover { background-color: #218838; }
    .reject-button {
        background-color: #dc3545;
        color: white; border: none;
        padding: 10px 20px; font-size: 16px;
        cursor: pointer; border-radius: 5px;
    }
    .reject-button:hover { background-color: #c82333; }
    .back-button {
        background-color: #007bff;
        color: white; border: none;
        padding: 10px 20px; font-size: 16px;
        cursor: pointer; border-radius: 5px;
        text-decoration: none; display: inline-block;
    }
    .back-button:hover { background-color: #0056b3; }
    .modal-actions { margin-top: 10px; }
    .submit-reject-button {
        background-color: #3b82f6;
        color: white; border: none;
        padding: 8px 16px; font-size: 16px;
        border-radius: 5px; cursor: pointer;
    }
    .submit-reject-button:hover {
        background-color: #2563eb;
    }
    #notif-icon {
        color: black; font-size: 24px;
    }
    /* Palakihin ang textarea sa loob ng reject-modal */
    #reject-reason {
        width: 95%;
        min-height: 150px;
        resize: vertical;
    }
</style>

<!-- ================== SCRIPTS ================== -->
<script>
// Search Table
function searchTable() {
    let input = document.getElementById("searchBar").value.toUpperCase();
    let table = document.querySelector("table tbody");
    let tr = table.getElementsByTagName("tr");

    for (let i = 0; i < tr.length; i++) {
        let td = tr[i].getElementsByTagName("td")[0];
        if (td) {
            let txtValue = td.textContent || td.innerText;
            tr[i].style.display = txtValue.toUpperCase().includes(input) ? "" : "none";
        }
    }
}

document.addEventListener("DOMContentLoaded", function () {
    var modal = document.getElementById("reservation-modal");
    var rejectModal = document.getElementById("reject-modal");
    var closeBtn = document.querySelector(".close-btn");
    var closeModalBtn = document.getElementById("close-modal");
    var approveButton = document.querySelector(".approve-button");
    var rejectButton = document.getElementById("reject-button");

    // Bawat reservation row
    document.querySelectorAll(".reservation-row").forEach(function (row) {
        row.addEventListener("click", function () {
            let status = this.getAttribute("data-status") || "N/A";

            // Dahil converted na sa PHP (may petsa at oras na), direkta na natin i-display
            let rawStartTime = this.getAttribute("data-start-time") || "N/A";
            let rawEndTime   = this.getAttribute("data-end-time")   || "N/A";

            document.getElementById("modal-start-time").textContent =
                "Start Time: " + rawStartTime;
            document.getElementById("modal-end-time").textContent =
                "End Time: " + rawEndTime;

            // Iba pang field
            document.getElementById("modal-name").textContent =
                "Name: " + (this.getAttribute("data-name") || "N/A");
            document.getElementById("modal-email").textContent =
                "Email: " + (this.getAttribute("data-email") || "N/A");
            document.getElementById("modal-event-type").textContent =
                "Event Type: " + (this.getAttribute("data-event-type") || "N/A");
            document.getElementById("modal-others").textContent =
                "Others: " + (this.getAttribute("data-others") || "N/A");
            document.getElementById("modal-event-place").textContent =
                "Event Place: " + (this.getAttribute("data-event-place") || "N/A");
            document.getElementById("modal-photo-size-layout").textContent =
                "Photo Size/Layout: " + (this.getAttribute("data-photo-size-layout") || "N/A");
            document.getElementById("modal-contact-number").textContent =
                "Contact Number: " + (this.getAttribute("data-contact-number") || "N/A");

            document.getElementById("modal-status").textContent =
                "Status: " + status;

            // Reject reason
            let reason = this.getAttribute("data-reason") || "";
            if (status.toLowerCase() === "rejected" && reason) {
                document.getElementById("modal-reason").textContent =
                    "Reason: " + reason;
                document.getElementById("modal-reason").style.display = "block";
            } else {
                document.getElementById("modal-reason").style.display = "none";
            }

            // Image
            let imagePath = this.getAttribute("data-image");
            document.getElementById("modal-image").src = imagePath
                ? "images/" + imagePath
                : "images/default.jpg";

            // Hidden input para maipasa ang reservation_id
            let reservationIdField = document.getElementById("modal-reservation-id");
            reservationIdField.value = this.getAttribute("data-reservation-id") || "";

            // Ipakita / itago Approve-Reject kung approved/rejected na
            if (status.toLowerCase() === "approved" || status.toLowerCase() === "rejected") {
                approveButton.style.display = "none";
                rejectButton.style.display = "none";
            } else {
                approveButton.style.display = "inline-block";
                rejectButton.style.display = "inline-block";
            }

            // Buksan ang modal
            modal.style.display = "flex";
        });
    });

    // Close main modal
    if (closeBtn) {
        closeBtn.onclick = function () {
            modal.style.display = "none";
        };
    }

    // Close kung click outside main modal
    window.onclick = function (event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
        if (event.target === rejectModal) {
            rejectModal.style.display = "none";
        }
    };

    // REJECT BUTTON
    if (rejectButton) {
        rejectButton.addEventListener("click", function () {
            let reservationId = document.getElementById("modal-reservation-id").value;
            if (!reservationId) {
                alert("Error: No reservation ID found.");
                return;
            }
            // Set reservation ID sa reject form
            document.getElementById("reject-reason").value = ""; 
            document.getElementById("reject-reservation-id").value = reservationId;
            // Ipakita ang reject-modal
            rejectModal.style.display = "flex";
        });
    }

    // Close reject modal
    if (closeModalBtn) {
        closeModalBtn.onclick = function () {
            rejectModal.style.display = "none";
        };
    }
});
</script>
</body>
</html>
