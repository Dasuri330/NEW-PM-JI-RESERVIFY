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

// Fetch payments data
$payments = [];
$sql = "SELECT 
    p.payment_id, p.reservation_id, p.message, 
    u.First_Name, u.Middle_Name, u.Last_Name, u.Email, u.Phone_Number, 
    p.Amount, p.ref_no, p.payment_method, p.payment_image, p.payment_type, 
    p.created_at, p.status, cn.reject_reason
FROM payment p
JOIN test_registration u ON p.user_id = u.id
LEFT JOIN (
    SELECT reservation_id, MAX(reject_reason) AS reject_reason 
    FROM customer_notifications 
    GROUP BY reservation_id
) cn ON p.reservation_id = cn.reservation_id
ORDER BY p.created_at ASC;
";

$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $payments[] = $row;
    }
    $stmt->close();
} else {
    echo "Error: " . $conn->error;
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                    <h1>Payment Status</h1>
                    <div style="display: flex; align-items: center; gap: 20px;">
                        <!-- Search bar -->
                        <input
                            type="text"
                            id="searchBar"
                            placeholder="Search reservation number.."
                            onkeyup="searchTable()"
                        >
                        <!-- Notification part -->
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

                <table>
                    <thead>
                        <tr>
                            <th>Payment Number</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    if (!empty($payments)) {
                        foreach ($payments as $payment) {
                            $payment_id   = htmlspecialchars($payment['payment_id'] ?? '');
                            $status       = htmlspecialchars($payment['status'] ?? '');
                            $status_color = ($status == 'approved') ? 'green' :
                                           (($status == 'rejected') ? 'red' : 'gray');
                            $reject_reason = htmlspecialchars($payment['reject_reason'] ?? '');

                            // Kunin ang raw created_at (hal. "2025-03-26 13:00:00")
                            $rawCreated   = $payment['created_at'] ?? '';

                            // I-convert sa gusto mong 12-hour format. Hal. "2025-03-26 01:00 PM"
                            $convertedCreated = date("Y-m-d h:i A", strtotime($rawCreated));

                            echo "
                            <tr class='reservation-row'
                                data-payment-id='{$payment_id}'
                                data-name='" . htmlspecialchars("{$payment['First_Name']} {$payment['Middle_Name']} {$payment['Last_Name']}") . "'
                                data-email='" . htmlspecialchars($payment['Email'] ?? '') . "'
                                data-contact-number='" . htmlspecialchars($payment['Phone_Number'] ?? '') . "'
                                data-amount='" . htmlspecialchars($payment['Amount'] ?? '') . "'
                                data-ref-no='" . htmlspecialchars($payment['ref_no'] ?? '') . "'
                                data-payment-method='" . htmlspecialchars($payment['payment_method'] ?? '') . "'
                                data-payment-image='" . htmlspecialchars($payment['payment_image'] ?? '') . "'
                                data-payment-type='" . htmlspecialchars($payment['payment_type'] ?? '') . "'
                                data-created-at='" . htmlspecialchars($convertedCreated) . "'
                                data-reason='" . ($status == 'rejected' ? $reject_reason : '') . "'
                                data-status='{$status}'
                            >
                                <td>{$payment_id}</td>
                                <td style='color: {$status_color};'>{$status}</td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='2'>No Payments found</td></tr>";
                    }
                    ?>
                    </tbody>
                </table>

                <!-- Main Modal for Payment Details -->
                <div id="reservation-modal" class="modal">
                    <div class="modal-content">
                        <span class="close-btn">&times;</span>
                        <h2 style="text-align: center;">Payment Details</h2>

                        <p id="modal-name"></p>
                        <p id="modal-email"></p>
                        <p id="modal-contact-number"></p>
                        <p id="modal-Amount"></p>
                        <p id="modal-ref-no"></p>
                        <p id="modal-payment-method"></p>
                        <p id="modal-payment-type"></p>
                        <p id="modal-created-at"></p>
                        <p id="modal-status"></p>
                        <img id="modal-image" src="" alt="Payment Image" style="max-width: 50%;">
                        <p id="modal-reason" style="color: red; font-weight: bold;"></p>

                        <form id="payment-action-form" action="handle_payment.php" method="POST">
                            <input type="hidden" name="payment_id" id="modal-payment-id">
                            <div class="button-container">
                                <!-- Approve button -->
                                <button
                                    type="submit"
                                    name="action"
                                    value="approve"
                                    class="approve-button"
                                    id="approve-payment-button"
                                >
                                    Approve
                                </button>
                                <!-- Reject button -->
                                <button
                                    type="button"
                                    id="reject-payment-button"
                                    class="reject-button"
                                >
                                    Reject
                                </button>
                                <!-- Back to Payments -->
                                <a href="admin_payments1.php" class="back-button">Back to Payments</a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Modal for Reject Reason -->
                <div id="reject-payment-modal" class="modal" style="display: none;">
                    <div class="modal-content">
                        <span class="close-button" id="close-reject-modal">&times;</span>
                        <h2>Reason for Rejection</h2>
                        <form id="reject-payment-form" action="handle_payment.php" method="POST">
                            <textarea
                                id="reject-payment-reason"
                                name="reject_reason"
                                rows="4"
                                cols="40"
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
                            <!-- Hidden input to carry payment_id -->
                            <input type="hidden" name="payment_id" id="reject-payment-id">
                        </form>
                    </div>
                </div>

            </main>
        </div>
    </div>
</div>

<!-- ================== STYLES ================== -->
<style>
    /* Basic styling adjustments */
    .reservation-row { cursor: pointer; }

    /* The main modal background */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0; top: 0;
        width: 100%; height: 100%;
        background-color: rgba(0, 0, 0, 0.4);
        align-items: center; justify-content: center;
    }
    .modal-content {
        background-color: #fefefe;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        max-height: 80vh;
        overflow-y: auto;
        color: black;
        border-radius: 10px;
    }
    .close-btn, .close-button {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }
    .close-btn:hover,
    .close-btn:focus,
    .close-button:hover,
    .close-button:focus {
        color: red;
        text-decoration: none;
    }
    #modal-image {
        max-width: 450px;
        max-height: 200px;
        display: block;
        border-radius: 10px;
        margin-top: 10px;
    }

    /* Buttons */
    .button-container {
        display: flex; gap: 10px;
        margin-top: 20px;
    }
    .approve-button {
        background-color: #28a745; color: white; border: none;
        padding: 10px 20px; font-size: 16px; cursor: pointer;
        border-radius: 5px;
    }
    .approve-button:hover {
        background-color: #218838;
    }
    .reject-button {
        background-color: #dc3545; color: white; border: none;
        padding: 10px 20px; font-size: 16px; cursor: pointer;
        border-radius: 5px;
    }
    .reject-button:hover {
        background-color: #c82333;
    }
    .back-button {
        background-color: #007bff; color: white; border: none;
        padding: 10px 20px; font-size: 16px; cursor: pointer;
        border-radius: 5px; text-decoration: none; display: inline-block;
    }
    .back-button:hover {
        background-color: #0056b3;
    }

    /* Reject modal */
    .modal-actions { margin-top: 10px; }
    .submit-reject-button {
        background-color: #3b82f6;
        color: white; border: none; padding: 10px 20px;
        margin-top: 15px; border-radius: 5px; cursor: pointer; font-size: 16px;
    }
    .submit-reject-button:hover {
        background-color: #2563eb;
    }

    /* Extra styling for the notification icon */
    #notif-icon {
        color: black;
        font-size: 24px;
    }

    /* I‐override ang laki ng .modal-content kapag nasa loob ng #reject-payment-modal */
    #reject-payment-modal .modal-content {
        width: 40%;
        max-width: 600px;
        margin: auto;
        max-height: 70vh;
        overflow-y: auto;
    }

    /* Pwede mo ring dagdagan ng padding sa textarea */
    #reject-payment-modal textarea#reject-payment-reason {
        width: 96%;
        min-height: 120px;
        resize: vertical;
        padding: 8px;
        font-size: 14px;
    }
</style>

<!-- ================== SCRIPTS ================== -->
<script>
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
    var rejectModal = document.getElementById("reject-payment-modal");
    var closeBtn = document.querySelector(".close-btn");
    var closeRejectBtn = document.getElementById("close-reject-modal");

    var approveButton = document.getElementById("approve-payment-button");
    var rejectButton = document.getElementById("reject-payment-button");

    // Bawat row
    document.querySelectorAll(".reservation-row").forEach(function (row) {
        row.addEventListener("click", function () {
            let paymentId = this.getAttribute("data-payment-id") || "";
            document.getElementById("modal-payment-id").value = paymentId;
            document.getElementById("reject-payment-id").value = paymentId;

            document.getElementById("modal-name").textContent =
                "Name: " + (this.getAttribute("data-name") || "N/A");
            document.getElementById("modal-email").textContent =
                "Email: " + (this.getAttribute("data-email") || "N/A");
            document.getElementById("modal-contact-number").textContent =
                "Contact Number: " + (this.getAttribute("data-contact-number") || "N/A");
            document.getElementById("modal-Amount").textContent =
                "Amount: " + (this.getAttribute("data-amount") || "N/A");
            document.getElementById("modal-ref-no").textContent =
                "Reference: " + (this.getAttribute("data-ref-no") || "N/A");
            document.getElementById("modal-payment-method").textContent =
                "Payment Method: " + (this.getAttribute("data-payment-method") || "N/A");
            document.getElementById("modal-payment-type").textContent =
                "Payment Type: " + (this.getAttribute("data-payment-type") || "N/A");
            document.getElementById("modal-created-at").textContent =
                "Created at: " + (this.getAttribute("data-created-at") || "N/A");

            // Kunin ang status
            var status = this.getAttribute("data-status") || "N/A";
            document.getElementById("modal-status").textContent = "Status: " + status;

            // Reject reason
            var reason = this.getAttribute("data-reason") || "";
            if (reason) {
                document.getElementById("modal-reason").textContent = "Reject Reason: " + reason;
            } else {
                document.getElementById("modal-reason").textContent = "";
            }

            // Payment image
            var imagePath = this.getAttribute("data-payment-image");
            document.getElementById("modal-image").src = imagePath
                ? "images/" + imagePath
                : "images/default.jpg";

            // Show the main modal
            modal.style.display = "flex";

            // Kung approved/rejected, hide buttons
            if (status === "approved" || status === "rejected") {
                approveButton.style.display = "none";
                rejectButton.style.display = "none";
            } else {
                approveButton.style.display = "inline-block";
                rejectButton.style.display = "inline-block";
            }
        });
    });

    // Close main modal
    if (closeBtn) {
        closeBtn.onclick = function () {
            modal.style.display = "none";
        };
    }

    // Approve button
    approveButton.addEventListener("click", function (e) {
        if (!confirm("Are you sure you want to approve this payment?")) {
            e.preventDefault();
        }
    });

    // Reject button => open the reject reason modal
    rejectButton.addEventListener("click", function () {
        rejectModal.style.display = "flex";
    });

    // Close reject modal
    if (closeRejectBtn) {
        closeRejectBtn.onclick = function () {
            rejectModal.style.display = "none";
        };
    }

    // Click outside to close
    window.onclick = function (event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
        if (event.target === rejectModal) {
            rejectModal.style.display = "none";
        }
    };
});
</script>
</body>
</html>
